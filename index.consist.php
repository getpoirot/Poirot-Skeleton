<?php
/**
 * This file separated that 3rd party application can instantiate this-
 * - and get system folder structure and autoload
 * ! also separate application with pre-startup
 */

## Define Unchangeable Consts:
#
define('TIME_REQUEST_MICRO', microtime(true));

!defined('PT_DIR_ROOT') && define('PT_DIR_ROOT', dirname(__FILE__), false);


## Setup autoLoading:
#
if ( file_exists(__DIR__.'/vendor/autoload.php') )
    require_once __DIR__.'/vendor/autoload.php';


require_once __DIR__.'/vendor/poirot-autoload.php';



## Set environment settings:
#
// factory environment profile
$dotEnv = \Poirot\Std\Environment\FactoryEnvironment::of(
    function() {
        return ($env_mode = getenv('PT_ENV_PROFILE')) ? $env_mode : 'default';
    }
);

// make it available through app. execution
\Poirot\Std\Environment\FactoryEnvironment::setCurrentEnvironment($dotEnv);

// apply environment system wide
$dotEnv->apply($overrideEnvironment);


## Changeable Consts: (maybe defined through .env)
#
define('PT_DIR_SKELETON', __DIR__);

// by default application folder is in www public
// it can be changed to any other folder like APP_DIR_WWW.'/../app-folder'
!defined('PT_DIR_SOURCE') && define('PT_DIR_SOURCE',       PT_DIR_SKELETON.'/src', false);
!defined('PT_DIR_CORE')   && define('PT_DIR_CORE',                PT_DIR_SOURCE.'/core', false);
!defined('PT_DIR_CONFIG') && define('PT_DIR_CONFIG',              PT_DIR_SOURCE.'/config', false);
!defined('PT_DIR_DATA')   && define('PT_DIR_DATA',                PT_DIR_SOURCE.'/data', false);
!defined('PT_DIR_TMP')    && define('PT_DIR_TMP',                    PT_DIR_DATA.'/tmp', false);

!defined('PT_DIR_VENDOR') && define('PT_DIR_VENDOR',       PT_DIR_ROOT.'/vendor', false);
