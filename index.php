<?php
namespace Poirot
{
    use Poirot\Core\ErrorStack;
    use Poirot\Core\PHPEnv;
    use Poirot\Core\PHPEnvFactory;

    (!defined('PHP_VERSION_ID') or PHP_VERSION_ID < 50306 ) and
    exit('Needs at least PHP5.3; your current php version is ' . phpversion() . '.');

    // Application Consistencies and AutoLoad:
    #  as separated file to used from 3rd party applications
    require_once __DIR__.'/index.consist.php';

    # change cwd to the application root by default
    chdir(__DIR__);


    // Set environment settings:
    $EnvSettings = PHPEnvFactory::factory(function() {
        $default = ($env_mode = getenv('POIROT_ENV_MODE')) ? $env_mode : 'default';
        return (defined('DEBUG') && constant('DEBUG')) ? 'dev' : $default;
    });
    $EnvSettings::setupSystemWide();


    // Run the application:
    ErrorStack::handleException('\Poirot\print_exception');
    ErrorStack::handleError(E_ERROR|E_RECOVERABLE_ERROR|E_USER_ERROR, function() {
        // handle runtime errors
        if ($errExpt = ErrorStack::handleDone()) throw $errExpt;
    });

    # start application:
    ioC()->get('sapi')->run();

    ErrorStack::handleDone();
    ErrorStack::handleDone();

    die(); // every soul shall taste of death
}
