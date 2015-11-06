<?php
/**
 * This file separated that 3rd party application can instantiate this-
 * - and get system folder structure and autoload
 * ! also separate application with pre-startup
 */

// Define Consts:
define('PR_REQUEST_MICROTIME', microtime(true));

define('PR_DEBUG', true);

define('PR_DIR_WWW'          , dirname(__FILE__) );
define('PR_DIR_THEME_DEFAULT', PR_DIR_WWW.'/theme' );
# by default application folder is in www public
# it can be changed to any other folder like APP_DIR_WWW.'/../app-folder'
define('PR_DIR_VENDOR',   	   PR_DIR_WWW.'/vendor');
define('PR_DIR_APPLICATION',  PR_DIR_WWW.'/_app');
define('PR_DIR_CORE', 			     PR_DIR_APPLICATION.'/modules');
define('PR_DIR_CONFIG', 		     PR_DIR_APPLICATION.'/config');
define('PR_DIR_TEMP', 			     PR_DIR_APPLICATION.'/tmp');

// Setup autoLoading:
if (file_exists(PR_DIR_VENDOR.'/autoload.php'))
    require_once PR_DIR_VENDOR.'/autoload.php';

if (file_exists(PR_DIR_VENDOR.'/poirot-autoload.php'))
    require_once PR_DIR_VENDOR.'/poirot-autoload.php';

## (!) Don't add something on lines below