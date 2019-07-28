<?php
## Access Application From Anywhere any Time
# 
namespace Poirot
{
    use IOC;
    use Poirot as P;
    use Poirot\Ioc\Container;

    if (! defined('FALLBACK_REQUIRED_POIROT') )
        // Only Access From Fallback Call Inside Poirot Skeleton
        die();


    ( !defined('PHP_VERSION_ID') or PHP_VERSION_ID < 50306 )
       && exit('Needs at least PHP5.3; your current php version is ' . phpversion() . '.');


    // Application Consistencies and AutoLoad:
    // as separated file to used from 3rd party applications
    require_once __DIR__.'/index.consist.php';


    ## start application:
    #
    $servicesConf = P\Std\Type\StdString::safeJoin(DS, PT_DIR_CONFIG, 'services');
    if ( false === $config = \Poirot\Config\load($servicesConf) )
        throw new \Exception(sprintf(
            'Cant Load IoC Services from config (%s)'
            , $servicesConf
        ));

    $IoC = new Container( new Container\BuildContainer($config) );
    IOC::GiveIoC($IoC);

    /** @var P\Application\Sapi $application */
    $application = IOC::Sapi();

    return $application;
}
