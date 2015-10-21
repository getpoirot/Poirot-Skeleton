<?php
namespace Poirot
{
    use Poirot\Application\Config;
    use Poirot\Container\Container;
    use Poirot\Container\ContainerBuilder;
    use Poirot\View\Interpreter\IsoRenderer;

    /**
     * IoC Container Gateway
     *
     * @return Container
     */
    function ioc() {
        static $IoC;
        if ($IoC)
            return $IoC;

        $config = new Config(APP_DIR_CONFIG);
        $IoC    = new Container(new ContainerBuilder($config->get('container')));
        $IoC->has('sapi.config') && $IoC->get('sapi.config')->from($config);

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

