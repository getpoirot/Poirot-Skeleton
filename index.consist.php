<?php
/**
 * This file separated that 3rd party application can instantiate this-
 * - and get system folder structure and autoload
 *
 */

// Define Consts:
define('REQUEST_MICROTIME', microtime(true));

define('DEBUG', true);

define('APP_DIR_WWW'          , dirname(__FILE__) );
define('APP_DIR_THEME_DEFAULT', APP_DIR_WWW.'/theme' );
# by default application folder is in www public
# it can be changed to any other folder like APP_DIR_WWW.'/../app-folder'
define('APP_DIR_VENDOR',   	   APP_DIR_WWW.'/vendor');
define('APP_DIR_APPLICATION',  APP_DIR_WWW.'/_app');
define('APP_DIR_CORE', 			     APP_DIR_APPLICATION.'/modules');
define('APP_DIR_CONFIG', 		     APP_DIR_APPLICATION.'/config');
define('APP_DIR_TEMP', 			     APP_DIR_APPLICATION.'/tmp');

// Setup autoLoading:
if (file_exists(APP_DIR_VENDOR.'/autoload.php'))
    require_once APP_DIR_VENDOR.'/autoload.php';

if (file_exists(APP_DIR_VENDOR.'/poirot-autoload.php'))
    require_once APP_DIR_VENDOR.'/poirot-autoload.php';

## (!) Don't add something on lines below