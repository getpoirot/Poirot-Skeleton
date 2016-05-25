<?php
namespace Poirot
{
    use Poirot\Ioc\Container;
    use Poirot\Std\ErrorStack;
    use Poirot\View\ViewModel\RendererPhp;

    /**
     * IoC Container Gateway
     *
     * @return Container
     */
    function IoC() {
        static $IoC;
        if ($IoC) return $IoC;

        $config = array();
        foreach (glob(PT_DIR_CONFIG.'/services.{,local.}conf.php', GLOB_BRACE) as $filePath) {
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
            ob_get_clean();

            ErrorStack::handleDone();
            ErrorStack::handleDone();
        }

        $IoC = new Container(new Container\BuildContainer($config));
        return $IoC;
    };

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
     * @param \Exception $e
     *
     * @throws \Exception cant render exception
     */
    function printException(\Exception $e) {
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

