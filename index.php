<?php
namespace
{
    # hope one day can use internal php server
    use Poirot\Application\Sapi as PoirotApplication;

    (!defined('PHP_VERSION_ID') or PHP_VERSION_ID < 50306 ) and
    exit('Needs at least PHP5.3; your current php version is ' . phpversion() . '.');

    // Application Consistencies and AutoLoad:
    #  as separated file to used from 3rd party applications
    require_once 'index.consist.php';

    # change cwd to the application root by default
    chdir(__DIR__);

    // Run the application:
    try {
        # merge application config with local one:
        $conf  = include_once APP_DIR_CONFIG.'/application.config.php';
        $local = APP_DIR_CONFIG.'/application.local.config.php';
        (!file_exists($local)) ?: (
            ($local = include_once $local) ? $conf = \Poirot\Core\array_merge($conf, $local) : null
        );

        # start application:
        $app  = new PoirotApplication($conf);
        $app->run();

    } catch (Exception $e) {
        throw $e;
    }

    die(); // every soul shall taste of death
}
