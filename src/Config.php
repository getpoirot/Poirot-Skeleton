<?php
namespace Poirot\Skeleton;

use Poirot\Std\Glob;
use Poirot\Std\ErrorStack;
use Poirot\Std\Type\StdArray;
use Poirot\Std\Type\StdString;


// TODO using Poirot\Config
class Config
{
    /** @var string */
    protected $path;

    private static $isProcessingLoading;


    /**
     * Construction
     *
     * - $configuration if it's dir/to/file then:
     *   it will looking for files "file.local.conf.php" and "file.conf.php"
     *
     * - if given argument is directory:
     *   load all files with extension ".local.conf.php" and ".conf.php"
     *
     * then fallback into default config dir (PT_DIR_CONFIG) with $configuration(path)
     * and merge loaded data
     *
     * - PT_SITE_NAME env variable will override configs fallback to that folder
     *
     *
     * @param string $configuration
     *
     */
    function __construct(string $configuration)
    {
        $this->path = rtrim($configuration, '\\/');
    }


    /**
     * Load Configs Properties From Given Configuration
     *
     *
     * @return StdArray|false
     */
    function load()
    {
        if ( $this->_isProcessingLoad() )
            throw new \RuntimeException(sprintf(
                'Conflict while loading config (%s); another process interrupt loading (%s).'
                    , $this->path
                    , static::$isProcessingLoading
            ));

        $this->_setFlagProcessingLoad();


        $path = $this->path;

        ## Try to load specified config
        #
        $config = $this->_loadGlobPattern($path);


        $fallBackDirectories = [];

        ## First try too load on initial system directory
        #
        $fallBackDir = rtrim( PT_DIR_CONFIG_INITIAL , '\\/' );
        if (strpos($fallBackDir, dirname($path)) !== 0)
            array_push(
                $fallBackDirectories
                , $fallBackDir
            );

        ## Looking in Default Config Directory
        #
        $fallBackDir = rtrim( PT_DIR_CONFIG , '\\/' );
        if (strpos($fallBackDir, dirname($path)) !== 0)
            array_push(
                $fallBackDirectories
                , $fallBackDir
            );


        if ($preConfig = $this->_loadFromDirectories($fallBackDirectories) )
            $config = ($config) ? $preConfig->withMergeRecursive($config) : $preConfig;


        $this->_setFlagProcessingLoad(false);
        return $config;
    }


    // ..

    private function _loadFromDirectories(array $directories)
    {
        $path = $this->path;


        $isLoaded = false;
        $config   = new StdArray;
        $fallBackDirectories = $directories;
        foreach ($fallBackDirectories as $fallBackDir)
        {
            $dirPath = dirname($path);

            $name = ltrim( basename($path) , '\\/' );

            $cnf = false;
            $stack = [$name];
            $dirPath = realpath( str_replace($fallBackDir, '', $dirPath) );
            $dirPath = explode(DS, trim($dirPath, DS));

            $maxDeep = 2;
            while (false === $cnf)
            {
                if ( 0 >= $maxDeep-- || false !== $cnf)
                    // Config loaded no need to deep load that.
                    break;

                $tPath = StdString::safeJoin(DS, ...array_merge([$fallBackDir], $stack));
                $cnf = $this->_loadGlobPattern($tPath);

                array_unshift($stack, array_pop($dirPath));
            }

            if ($cnf) {
                $config = $config->withMergeRecursive($cnf, false);
                $isLoaded |= true;
            }


            // Check for multi-site config within each directory
            //
            if ( $siteName = getenv('PT_SITE_NAME') )
            {
                $siteName = strtolower($siteName);

                if ( $siteName != 'false' ) {
                    $tPath = StdString::safeJoin(DS, PT_DIR_CONFIG, '_site', $siteName);
                    $cnf = $this->_loadGlobPattern($tPath);

                    if ($cnf) {
                        $config = $config->withMergeRecursive($cnf, false);
                        $isLoaded |= true;
                    }
                }
            }
        }


        return ($isLoaded) ? $config : false;
    }

    private function _loadGlobPattern($path, StdArray $defaults = null)
    {
        $isLoaded = false;
        $config   = ($defaults) ? $defaults : new StdArray;


        $globPattern = $path;
        if ( is_dir($path) ) {
            $globPattern = str_replace('\\', '/', $globPattern); // normalize path separator
            $globPattern = rtrim($globPattern, DS).DS.'*';
        }

        if (! is_file($path) )
            // did not given exactly name of file
            $globPattern .= '.{,local.}conf.php';


        foreach ( Glob::glob($globPattern, GLOB_BRACE) as $filePath )
        {
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

        return ($isLoaded) ? $config : false;
    }


    private function _isProcessingLoad()
    {
        return static::$isProcessingLoading;
    }

    private function _setFlagProcessingLoad(bool $flag = true)
    {
        static::$isProcessingLoading = ($flag) ? $this->path : false;
    }
}
