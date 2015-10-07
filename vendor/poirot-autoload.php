<?php
// poirot-autoload.php @default poirot autoload

// TODO using compiled cache autoload

require_once __DIR__ . '/poirot/loader/' . '/Poirot/Loader/Autoloader/AggregateAutoloader.php';

$namespaces = __DIR__.'/poirot-autoload-namespaces.php';
$classmap   = __DIR__.'/poirot-autoload-classmap.php';

$loader = new \Poirot\Loader\Autoloader\AggregateAutoloader([
    'NamespaceAutoloader' => $namespaces,
    'ClassMapAutoloader'  => $classmap,
]);

## prepend poirot autoloader to autoload stack
$loader->register(true);
