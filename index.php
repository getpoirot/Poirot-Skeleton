<?php
namespace Poirot
{
    use Poirot as P;
    
    (!defined('PHP_VERSION_ID') or PHP_VERSION_ID < 50306 )
    and exit('Needs at least PHP5.3; your current php version is ' . phpversion() . '.');

    // Application Consistencies and AutoLoad:
    #  as separated file to used from 3rd party applications
    require_once __DIR__.'/index.consist.php';

    # change cwd to the application root by default
    chdir(__DIR__);


    // Set environment settings:
    P\Std\Environment\FactoryEnvironment::of(function() {
        $default = ($env_mode = getenv('PT_ENVIRONMENT'))        ? $env_mode : 'default';
        return     (defined('PT_DEBUG') && constant('PT_DEBUG')) ? 'dev'     : $default;
    })->apply();


    // Run the application:
    P\Std\ErrorStack::handleError(E_ERROR|E_RECOVERABLE_ERROR|E_USER_ERROR, function($error) { throw $error; });
    P\Std\ErrorStack::handleException(function ($error) { printException($error); die; });

    # start application:
    $application = IoC()->get('sapi');
    $application->run();
}
