<?php
namespace
{
    use Poirot\Application\Config;
    use Poirot\Application\Sapi as PoirotApplication;
    use Poirot\ErrorHandling\SettingsFactory;
    use Poirot\View\Interpreter\IsoRenderer;

    (!defined('PHP_VERSION_ID') or PHP_VERSION_ID < 50306 ) and
    exit('Needs at least PHP5.3; your current php version is ' . phpversion() . '.');

    // Application Consistencies and AutoLoad:
    #  as separated file to used from 3rd party applications
    require_once __DIR__.'/index.consist.php';

    # change cwd to the application root by default
    chdir(__DIR__);

    // Run the application:
    try {
        # set environment settings
        $EnvSettings = SettingsFactory::factory(function() {
            // TODO detect from env global exact name of settings
            return (defined('DEBUG') && constant('DEBUG')) ? 'dev' : 'default' /* 'prod' */;
        });
        $EnvSettings::setupSystemWide();

        # start application:
        $app  = new PoirotApplication(new Config(APP_DIR_CONFIG));
        $app->run();
    ## beauty exception messages
    } catch (Exception $e) {
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

    die(); // every soul shall taste of death
}
