<?php
namespace Poirot
{
    use IOC;
    use Poirot as P;
    use Poirot\Ioc\Container;

    (!defined('PHP_VERSION_ID') or PHP_VERSION_ID < 50306 )
    and exit('Needs at least PHP5.3; your current php version is ' . phpversion() . '.');

    // Application Consistencies and AutoLoad:
    #  as separated file to used from 3rd party applications
    require_once __DIR__.'/index.consist.php';

    # change cwd to the application root by default
    chdir(PT_DIR_ROOT);

    // Run the application:
    P\Std\ErrorStack::handleError(E_ERROR|E_RECOVERABLE_ERROR|E_USER_ERROR, function($error) { throw $error; });
    P\Std\ErrorStack::handleException(function ($error) { echo new DecorateExceptionResponse($error); die; });
    P\Std\ErrorStack::handleException(function ($error) {
        if (PHP_SAPI == 'cli') { echo $error->getMessage(); die; }
        throw $error; // pass it to up chain to handle
    });

    # start application:
    $config = \Poirot\Config\load(PT_DIR_CONFIG.'/services');
    $IoC    = new Container( new Container\BuildContainer($config) );
    IOC::GiveIoC($IoC);

    /** @var P\Application\Sapi $application */
    $application = IOC::Sapi();
    $application->run();
}
