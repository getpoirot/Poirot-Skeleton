<?php
/**
 * This file separated that 3rd party application can instantiate this-
 * - and get system folder structure and autoload
 * ! also separate application with pre-startup
 */

// Define Consts:
define('PT_TIME_REQUEST_MICRO', microtime(true));

define('PT_DEBUG', ($debug = getenv('PT_DEBUG')) ? $debug : true);

define('PT_DIR_WWW'          , dirname(__FILE__) );
define('PT_DIR_THEME_DEFAULT', PT_DIR_WWW.'/theme' );
# by default application folder is in www public
# it can be changed to any other folder like APP_DIR_WWW.'/../app-folder'
define('PT_DIR_VENDOR',       PT_DIR_WWW.'/vendor');
define('PT_DIR_SOURCE',       PT_DIR_WWW.'/src');
define('PT_DIR_CORE',                PT_DIR_SOURCE.'/core');
define('PT_DIR_CONFIG',              PT_DIR_SOURCE.'/config');
define('PT_DIR_TEMP',                PT_DIR_SOURCE.'/tmp');

// Setup autoLoading:
if (file_exists(PT_DIR_VENDOR.'/autoload.php'))
    require_once PT_DIR_VENDOR.'/autoload.php';

if (file_exists(PT_DIR_VENDOR.'/poirot-autoload.php'))
    require_once PT_DIR_VENDOR.'/poirot-autoload.php';

## (!) Don't add something on lines below