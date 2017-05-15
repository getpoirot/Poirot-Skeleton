<?php
/* poirot-autoload.php @default poirot autoload */

define('DIR_VENDOR', DIR_ROOT . '/vendor');


if (! class_exists('\Poirot\Loader\Autoloader\LoaderAutoloadAggregate') )
    // Used as base skeleton; it may also installed as composer package so the required packages is available.
    require_once DIR_VENDOR. '/poirot/loader' . '/Poirot/Loader/Autoloader/LoaderAutoloadAggregate.php';


// TODO using compiled cache autoload

$namespaces = __DIR__.'/poirot-autoload-namespaces.php';
$classmap   = __DIR__.'/poirot-autoload-classmap.php';

$loader = new \Poirot\Loader\Autoloader\LoaderAutoloadAggregate(array(
    'Poirot\Loader\Autoloader\LoaderAutoloadClassMap'  => $classmap,
    'Poirot\Loader\Autoloader\LoaderAutoloadNamespace' => $namespaces,
));

$loader->register(true); // true for prepend loader in autoload stack


// Poirot Functions:
require_once __DIR__ . '/poirot-functions.php';
