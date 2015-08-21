<?php
namespace
{
    use Poirot\Application\Sapi as PoirotApplication;

    (!defined('PHP_VERSION_ID') or PHP_VERSION_ID < 50306 ) and
    exit('Needs at least PHP5.3; your current php version is ' . phpversion() . '.');

    // Application Consistencies and AutoLoad:
    #  as separated file to used from 3rd party applications
    require_once __DIR__.'/index.consist.php';

    # change cwd to the application root by default
    chdir(__DIR__);

    // Run the application:
    try {
        # merge application config:
        $config = [];
        $conFiles = APP_DIR_CONFIG .DS. '*.{,local.}conf.php';

        ob_start();
        set_error_handler(function($errno, $errstr) {
            throw new \ErrorException($errstr, $errno);
        }, E_ALL);
        foreach (glob($conFiles, GLOB_BRACE) as $file) {
            $hostConf = include $file;
            $config = \Poirot\Core\array_merge($config, $hostConf);
        }
        restore_error_handler();
        ob_get_clean();

        # start application:
        $app  = new PoirotApplication($config);
        $app->run();

    } catch (Exception $e) {
        throw $e;
    }

    die(); // every soul shall taste of death
}
