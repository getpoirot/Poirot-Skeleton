<?php
namespace Poirot
{
    use Poirot\Container\Container;
    use Poirot\Container\ContainerBuilder;
    use Poirot\View\Interpreter\IsoRenderer;

    /**
     * IoC Container Gateway
     *
     * @return Container
     */
    function ioC() {
        static $IoC;
        if ($IoC)
            return $IoC;

        $config = include_once APP_DIR_CONFIG.'/services.conf.php';
        $IoC    = new Container(new ContainerBuilder($config));

        return $IoC;
    };

    /**
     * Print Exception Object Error Page
     *
     * @param \Exception $e
     *
     * @throws \Exception cant render exception
     */
    function print_exception(\Exception $e) {
        if (ob_get_level())
            ## clean output buffer, display just error page
            ob_end_clean();
        try {
            echo (new IsoRenderer())->capture(
                APP_DIR_THEME_DEFAULT.'/error/general.php'
                , ['exception' => $e]
            );
        } catch(\Exception $ve) {
            ## throw exception if can't render template
            throw $e;
        }
    }
}

