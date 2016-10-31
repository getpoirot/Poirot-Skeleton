<?php
/**
 * This file separated that 3rd party application can instantiate this-
 * - and get system folder structure and autoload
 * ! also separate application with pre-startup
 */

// Define Unchangeable Consts:
define('TIME_REQUEST_MICRO', microtime(true));

$debug = getenv('DEBUG');
define('DEBUG', ($debug && filter_var($debug, FILTER_VALIDATE_BOOLEAN)) ? $debug : false, false);

define('DIR_SKELETON', dirname(__FILE__), false);
define('DIR_VENDOR',       DIR_SKELETON.'/vendor', false);

// Setup autoLoading:
if (file_exists(DIR_VENDOR.'/autoload.php'))
    require_once DIR_VENDOR.'/autoload.php';

if (file_exists(DIR_VENDOR.'/poirot-autoload.php'))
    require_once DIR_VENDOR.'/poirot-autoload.php';


// Set environment settings:
$dotEnv = __DIR__.'/.env.php';
$overrideEnvironment = (is_readable($dotEnv)) ? include_once $dotEnv : array();
\Poirot\Std\Environment\FactoryEnvironment::of(function() {
    $default = ($env_mode = getenv('PT_ENVIRONMENT'))  ? $env_mode : 'default';
    return     (defined('DEBUG') && constant('DEBUG')) ? 'dev'     : $default;
})->apply($overrideEnvironment);

// Changeable Consts: (maybe defined through .env)

!defined('PT_DIR_WWW') && define('PT_DIR_WWW', DIR_SKELETON, false);
!defined('PT_DIR_THEME_DEFAULT') && define('PT_DIR_THEME_DEFAULT', PT_DIR_WWW.'/theme', false);

# by default application folder is in www public
# it can be changed to any other folder like APP_DIR_WWW.'/../app-folder'
!defined('PT_DIR_SOURCE') && define('PT_DIR_SOURCE',       DIR_SKELETON.'/src', false);
!defined('PT_DIR_CORE')   && define('PT_DIR_CORE',                PT_DIR_SOURCE.'/core', false);
!defined('PT_DIR_CONFIG') && define('PT_DIR_CONFIG',              PT_DIR_SOURCE.'/config', false);
!defined('PT_DIR_DATA')   && define('PT_DIR_DATA',                PT_DIR_SOURCE.'/data', false);
!defined('PT_DIR_TMP')    && define('PT_DIR_TMP',                    PT_DIR_DATA.'/tmp', false);
