<?php
namespace Poirot
{

    use Poirot\Std\Environment\EnvServerDefault;
    use Poirot\View\ViewModel\RendererPhp;


    class DecorateExceptionResponse
    {
        /** @var \Exception */
        protected $e;

        /**
         * Constructor.
         * @param \Exception $e
         */
        function __construct($e)
        {
            if (!($e instanceof \Throwable || $e instanceof \Exception) )
                throw new \InvalidArgumentException(sprintf(
                    'Invalid Argument (%s) must be an \Exception or \Throwable.'
                    , get_class($e)
                ));

            $this->e = $e;
        }

        function __toString()
        {
            $e = $this->e;

            if (isset($_SERVER['HTTP_ACCEPT']) && $_SERVER['HTTP_ACCEPT'] == 'application/json') {
                $exception_code = $e->getCode();

                $exRef = new \ReflectionClass($e);
                $result = array(
                    'status' => 'ERROR',
                    'error'  => array(
                        'state'   => $exRef->getShortName(),
                        'code'    => $exception_code,
                        'message' => $e->getMessage(),
                    ),
                );

                $isAllowDisplayExceptions = new EnvServerDefault();
                $isAllowDisplayExceptions = $isAllowDisplayExceptions->getErrorReporting();

                if ($isAllowDisplayExceptions) {
                    do {
                        $result = array_merge_recursive($result, array(
                            'error' => array(
                                '_debug_' => array(
                                    'exception' => array(
                                        array(
                                            'message' => $e->getMessage(),
                                            'class'   => get_class($e),
                                            'file'    => $e->getFile(),
                                            'line'    => $e->getLine(),
                                        ),
                                    ),
                                ),
                            ),
                        ));
                    } while ($e = $e->getPrevious());
                }

                \Poirot\Http\Response\httpResponseCode(500);
                header('Content-Type: application/json');
                echo json_encode($result);
                die;
            }

            if (ob_get_level())
                ## clean output buffer, display just error page
                ob_end_clean();

            try {
                return $this->toHtml();
            } catch(\Exception $ve) {
                ## throw exception if can't render template
                return sprintf(
                    'Error While Rendering Exception Into HTML!!! (%s)'
                    , $e->getMessage()
                );
            }
        }

        /**
         * Print Exception Object Error Page
         *
         * @return string
         * @throws \Throwable
         */
        function toHtml()
        {
            $e = $this->e;

            try {
                $renderer = new RendererPhp();
                return $renderer->capture(
                    __DIR__ . '/../.error.page.php'
                    , array('exception' => $e)
                );
            } catch(\Exception $ve) {
                ## throw exception if can't render template
                throw $e;
            }
        }
    }


    /**
     * Is Sapi Command Line?
     *
     * @return bool
     */
    function isCommandLine($sapiName = null)
    {
        if ($sapiName === null)
            $sapiName = php_sapi_name();

        return ( strpos($sapiName, 'cli') === 0 );
    }
}

namespace Poirot\Config
{
    use Poirot\Std\ErrorStack;
    use function Poirot\Std\generateShuffleCode;
    use Poirot\Std\Glob;
    use Poirot\Std\Type\StdArray;
    use Poirot\Std\Type\StdString;

    /**
     * Load Config Files From Given Directory
     *
     * - file name can be in form of dir/to/file then:
     *   it will looking for files
     *   "file.local.conf.php" and "file.conf.php"
     *
     * - if given argument is directory:
     *   load all files with extension
     *   ".local.conf.php" and ".conf.php"
     *
     * - then fallback into default config dir (PT_DIR_CONFIG)
     *   with basename(path) and merge loaded data
     *
     * - PT_SITE_NAME override configs by set env name
     *
     * @param string $path file or dir path
     *
     * @return StdArray|false
     */
    function load($path, $once = false, $raceHash = null)
    {
        static $RACE_CHECK_HASH;

        if (null == $RACE_CHECK_HASH)
            // fresh load call
            $RACE_CHECK_HASH = generateShuffleCode();

        elseif($raceHash !== $RACE_CHECK_HASH)
            throw new \RuntimeException('Conflict while loading config; another process interupt corent one');


        $isLoaded = false;
        $config   = new StdArray;

        if (strpos($path, rtrim( PT_DIR_CONFIG_INITIAL , '\\/' )) !== 0) {
            // First try too load on initial system directory
            $fallBackDirectories = [rtrim( PT_DIR_CONFIG_INITIAL , '\\/' ), ];

            // Check for multi-site config
            //
            if ( $siteName = getenv('PT_SITE_NAME') )
            {
                $siteName = strtolower($siteName);

                if ( $siteName != 'false' )
                    array_push(
                        $fallBackDirectories
                        , StdString::safeJoin(DS, PT_DIR_CONFIG, '_site', $siteName)
                    );
            }

            foreach ($fallBackDirectories as $fallBackDir)
            {
                $dirPath = dirname($path);

                if (strpos($dirPath, $fallBackDir) !== 0)
                {
                    $name = ltrim( basename($path) , '\\/' );

                    $cnf = false;
                    $stack = [$name];
                    $dirPath = realpath( str_replace($fallBackDir, '', $dirPath) );
                    $dirPath = explode(DS, ltrim($dirPath, DS));

                    $maxDeep = 2;
                    while (false === $cnf) {
                        if ( 0 >= $maxDeep-- )
                            break;

                        $tPath = StdString::safeJoin(DS, ...array_merge([$fallBackDir], $stack));
                        $cnf   = load($tPath, true, $RACE_CHECK_HASH);
                        array_unshift($stack, array_pop($dirPath));
                    }

                    if ($cnf) {
                        $config = $config->withMergeRecursive($cnf, false);
                        $isLoaded |= true;
                    }
                }
            }
        }


        $globPattern = $path;
        if ( is_dir($path) ) {
            $globPattern = str_replace('\\', '/', $globPattern); // normalize path separator
            $globPattern = rtrim($globPattern, DS).DS.'*';
        }

        if (! is_file($path) )
            // did not given exactly name of file
            $globPattern .= '.{,local.}conf.php';

        foreach ( Glob::glob($globPattern, GLOB_BRACE) as $filePath ) {
            ErrorStack::handleException(function ($e) use ($filePath) {
                ob_end_clean();
                throw new \RuntimeException(
                    sprintf('Error while loading config: %s', $filePath)
                    , 0
                    , $e
                );
            });

            ErrorStack::handleError(E_ALL, function ($e){ throw $e; });

            ob_start();
            $fConf = include $filePath;
            if (! is_array($fConf) )
                throw new \RuntimeException(sprintf(
                    'Config file (%s) must provide array; given (%s).'
                    , $filePath
                    , \Poirot\Std\flatten($fConf)
                ));
            ob_get_clean();

            $config = $config->withMergeRecursive($fConf, false);

            ErrorStack::handleDone();
            ErrorStack::handleDone();

            $isLoaded |= true;
        }


        ## Looking in Default Config Directory
        #
        // TODO here ..............

        if (! $once) {
            $fallBackDirectories = [rtrim( PT_DIR_CONFIG, '\\/' ), ];

            // Check for multi-site config
            //
            if ( $siteName = getenv('PT_SITE_NAME') )
            {
                $siteName = strtolower($siteName);

                if ( $siteName != 'false' )
                    array_push(
                        $fallBackDirectories
                        , StdString::safeJoin(DS, PT_DIR_CONFIG, '_site', $siteName)
                    );
            }

            foreach ($fallBackDirectories as $fallBackDir)
            {
                $dirPath = dirname($path);

                if (strpos($dirPath, $fallBackDir) !== 0)
                {
                    $name = ltrim( basename($path) , '\\/' );

                    $cnf = false;
                    $stack = [$name];
                    $dirPath = realpath( str_replace($fallBackDir, '', $dirPath) );
                    $dirPath = explode(DIRECTORY_SEPARATOR, ltrim($dirPath, DIRECTORY_SEPARATOR));

                    $maxDeep = 2;
                    while (false === $cnf) {
                        if ( 0 >= $maxDeep-- )
                            break;

                        $tPath = $fallBackDir.'/'.implode('/', $stack);
                        $cnf   = load($tPath, true, $RACE_CHECK_HASH);
                        array_unshift($stack, array_pop($dirPath));
                    }

                    if ($cnf) {
                        $config = $config->withMergeRecursive($cnf, false);
                        $isLoaded |= true;
                    }
                }
            }
        }

        if (null === $raceHash)
            $RACE_CHECK_HASH = null;


        return ($isLoaded) ? $config : false;
    }
}
