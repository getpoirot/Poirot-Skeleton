<?php
// poirot-autoload.php @default poirot autoload

// TODO using compiled cache autoload

require_once __DIR__ . '/poirot/loader/' . '/Poirot/Loader/Autoloader/AggregateAutoloader.php';

$loader = new \Poirot\Loader\Autoloader\AggregateAutoloader();

$namespaces = __DIR__.'/poirot-autoload-namespaces.php';
if (file_exists($namespaces)) {
    $namespaces = include_once $namespaces;
    $loader->loader('Poirot\Loader\Autoloader\NamespaceAutoloader')
        ->setStackArray($namespaces);
}

$classmap = __DIR__.'/poirot-autoload-classmap.php';
if (file_exists($classmap)) {
    $classmap = include_once $classmap;
    $loader->loader('Poirot\Loader\Autoloader\ClassMapAutoloader')
        ->setMapArray($classmap);
}

## prepend autoload register
$loader->register(true);
