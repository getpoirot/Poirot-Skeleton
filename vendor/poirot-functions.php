<?php
namespace Poirot
{
    use Poirot\View\ViewModel\RendererPhp;

    /**
     * Is Sapi Command Line?
     *
     * @return bool
     */
    function isCommandLine()
    {
        return ( strpos(php_sapi_name(), 'cli') === 0 );
    }

    /**
     * Print Exception Object Error Page
     *
     * @param \Exception|\Throwable $e
     *
     * @throws \Throwable
     */
    function printException($e) {
        if (ob_get_level())
            ## clean output buffer, display just error page
            ob_end_clean();
        try {
            $renderer = new RendererPhp();
            echo $renderer->capture(
                PT_DIR_THEME_DEFAULT.'/error/general.php'
                , array('exception' => $e)
            );
        } catch(\Exception $ve) {
            ## throw exception if can't render template
            throw $e;
        }
    }
}

namespace Poirot\Config
{
    use Poirot\Std\ErrorStack;
    use Poirot\Std\Type\StdArray;

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
     * @param string $dirOrFile file or dir path
     *
     * @return StdArray
     */
    function load($dirOrFile)
    {
        $config = new StdArray();
        
        $globPattern = $dirOrFile;
        if (is_dir($dirOrFile)) {
            $globPattern = str_replace('\\', '/', $globPattern); // normalize path separator
            $globPattern = rtrim($globPattern, '/').'/*';
        }
        if (!is_file($dirOrFile))
            $globPattern .= '.{,local.}conf.php';

        foreach (glob($globPattern, GLOB_BRACE) as $filePath) {
            ErrorStack::handleException(function ($e) use ($filePath) {
                ob_end_clean();
                throw new \RuntimeException(
                    sprintf('Error while loading config: %s', $filePath)
                    , $e
                );
            });

            ErrorStack::handleError(E_ALL, function ($e){ throw $e; });

            ob_start();
            $fConf = include_once $filePath;
            if (!is_array($fConf)) throw new \RuntimeException('Config file must provide array.');
            $config = $config->withMergeRecursive($fConf);
            ob_get_clean();

            ErrorStack::handleDone();
            ErrorStack::handleDone();
        }

        return $config;
    }
}
