<?php
/**
 * This file separated that 3rd party application can instantiate this-
 * - and get system folder structure and autoload
 *
 */

// Define Consts
define('REQUEST_MICROTIME', microtime(true));
define('DS', DIRECTORY_SEPARATOR);

define('APP_DIR_ROOT'       , dirname(__FILE__) );
define('APP_DIR_APPLICATION', APP_DIR_ROOT .DS. '_app');
define('APP_DIR_LIBRARIES',   	APP_DIR_APPLICATION .DS. 'vendor');
define('APP_DIR_MODULES', 		APP_DIR_APPLICATION .DS. 'vendor');
define('APP_DIR_CORE', 			APP_DIR_APPLICATION .DS. 'modules');
define('APP_DIR_CONFIG', 		APP_DIR_APPLICATION .DS. 'config');
define('APP_DIR_TEMP', 			APP_DIR_APPLICATION .DS. 'tmp');

// Setup autoLoading
if (file_exists(APP_DIR_LIBRARIES .DS. 'autoload.php'))
    require_once APP_DIR_LIBRARIES .DS. 'autoload.php';
