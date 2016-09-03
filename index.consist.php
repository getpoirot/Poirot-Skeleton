<?php
/**
 * This file separated that 3rd party application can instantiate this-
 * - and get system folder structure and autoload
 * ! also separate application with pre-startup
 */

// Define Unchangeable Consts:
define('PT_TIME_REQUEST_MICRO', microtime(true));

$debug = getenv('PT_DEBUG');
define('PT_DEBUG', ($debug && filter_var($debug, FILTER_VALIDATE_BOOLEAN)) ? $debug : false, false);

define('PT_DIR_WWW'          , dirname(__FILE__), false);
define('PT_DIR_VENDOR',       PT_DIR_WWW.'/vendor', false);

// Setup autoLoading:
if (file_exists(PT_DIR_VENDOR.'/autoload.php'))
    require_once PT_DIR_VENDOR.'/autoload.php';

if (file_exists(PT_DIR_VENDOR.'/poirot-autoload.php'))
    require_once PT_DIR_VENDOR.'/poirot-autoload.php';


// Set environment settings:
$dotEnv = __DIR__.'/.env.php';
$overrideEnvironment = (is_readable($dotEnv)) ? include_once $dotEnv : array();
\Poirot\Std\Environment\FactoryEnvironment::of(function() {
    $default = ($env_mode = getenv('PT_ENVIRONMENT'))        ? $env_mode : 'default';
    return     (defined('PT_DEBUG') && constant('PT_DEBUG')) ? 'dev'     : $default;
})->apply($overrideEnvironment);

// Changeable Consts: (maybe defined through .env)

!defined('PT_DIR_THEME_DEFAULT') && define('PT_DIR_THEME_DEFAULT', PT_DIR_WWW.'/theme', false);
# by default application folder is in www public
# it can be changed to any other folder like APP_DIR_WWW.'/../app-folder'
!defined('PT_DIR_SOURCE') && define('PT_DIR_SOURCE',       PT_DIR_WWW.'/src', false);
!defined('PT_DIR_CORE')   && define('PT_DIR_CORE',                PT_DIR_SOURCE.'/core', false);
!defined('PT_DIR_CONFIG') && define('PT_DIR_CONFIG',              PT_DIR_SOURCE.'/config', false);
!defined('PT_DIR_DATA')   && define('PT_DIR_DATA',                PT_DIR_SOURCE.'/data', false);
!defined('PT_DIR_TMP')    && define('PT_DIR_TMP',                    PT_DIR_DATA.'/tmp', false);
