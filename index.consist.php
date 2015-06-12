<?php
/**
 * This file separated that 3rd party application can instantiate this-
 * - and get system folder structure and autoload
 *
 */

// Define Consts
define('REQUEST_MICROTIME', microtime(true));
define('DS', DIRECTORY_SEPARATOR);

define('APP_DIR_WWW'        , dirname(__FILE__) );
// by default application folder is in www public
// it can be changed to any other folder like APP_DIR_WWW.'/../app-folder'
define('APP_DIR_APPLICATION', APP_DIR_WWW .DS. '_app');
define('APP_DIR_VENDOR',   	APP_DIR_APPLICATION .DS. 'vendor');
define('APP_DIR_CORE', 			APP_DIR_APPLICATION .DS. 'modules');
define('APP_DIR_CONFIG', 		APP_DIR_APPLICATION .DS. 'config');
define('APP_DIR_TEMP', 			APP_DIR_APPLICATION .DS. 'tmp');

// Setup autoLoading
if (file_exists(APP_DIR_VENDOR .DS. 'autoload.php'))
    require_once APP_DIR_VENDOR .DS. 'autoload.php';
