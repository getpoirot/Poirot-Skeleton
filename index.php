<?php
namespace
{
    use Poirot\Application\Sapi;

    (!defined('PHP_VERSION_ID') or PHP_VERSION_ID < 50306 ) and
    exit('Needs at least PHP5.3; your current php version is ' . phpversion() . '.');

    /*
     * Application Consistencies and AutoLoad
     */
    require_once 'index.consist.php';

    /*
     * change cwd to the application root by default
     */
    chdir(__DIR__);

    // Run the application!
    $app = new Sapi();
    try {
        $app->run();

    } catch (Exception $e) {
        throw $e;
    }

    die(); // every soul shall taste of death
}
