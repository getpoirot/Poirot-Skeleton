<?php
namespace
{
    (!defined('PHP_VERSION_ID') or PHP_VERSION_ID < 50306 ) and
    exit('Needs at least PHP5.3; your current php version is ' . phpversion() . '.');

    /*
     * Application Consistencies and AutoLoad
     */
    require 'index.consist.php';
    (APP_DIR_WWW !== dirname(__FILE__))
        and die('put index.consist file beside '.basename(__FILE__));

    /*
     * change cwd to the application root by default
     */
    chdir(__DIR__);

    // Run the application!
    try {

    }
    catch (Exception $e) {
        throw $e;
    }
}
