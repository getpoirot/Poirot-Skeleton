<?php
/* poirot-autoload.php @default poirot autoload */

// Autoload Default Poirot Libraries:
use Poirot\Loader\Autoloader\LoaderAutoloadClassMap;
use Poirot\Loader\Autoloader\LoaderAutoloadNamespace;

// TODO using compiled cache autoload

if (! class_exists('\Poirot\Loader\Autoloader\LoaderAutoloadAggregate') )
    // Used as base skeleton; it may also installed as composer package so the required packages is available.
    require_once DIR_ROOT . '/vendor/poirot/loader' . '/Poirot/Loader/Autoloader/LoaderAutoloadAggregate.php';

$namespaces = __DIR__.'/poirot-autoload-namespaces.php';
$classmap   = __DIR__.'/poirot-autoload-classmap.php';

$loader = new \Poirot\Loader\Autoloader\LoaderAutoloadAggregate(array(
    'attach' => array(
        array('loader' => new LoaderAutoloadClassMap,  'priority' => 100),
        array('loader' => new LoaderAutoloadNamespace, 'priority' => 50),
    ),
    'Poirot\Loader\Autoloader\LoaderAutoloadClassMap'  => $namespaces,
    #Poirot\Loader\Autoloader\LoaderAutoloadClassMap::class  => $namespaces,
    'Poirot\Loader\Autoloader\LoaderAutoloadNamespace' => $classmap,
    #Poirot\Loader\Autoloader\LoaderAutoloadNamespace::class => $classmap,
));

$loader->register(true); // true for prepend loader in autoload stack


// Poirot Functions:
require_once __DIR__ . '/poirot-functions.php';
