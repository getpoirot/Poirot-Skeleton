<?php
namespace
{
    (!defined('PHP_VERSION_ID') or PHP_VERSION_ID < 50306 ) and
    exit('Needs at least PHP5.3; youre current php version is ' . phpversion() . '.');

    /**
     * This makes our life easier when dealing with paths. Everything is relative
     * to the application root now.
     */
    chdir(__DIR__);

    /**
     * Application Consistencies and AutoLoad
     *
     */
    require 'index.consist.php';

    // Run the application!
    try {

    }
    catch (Exception $e) {
        throw $e;
    }
}
