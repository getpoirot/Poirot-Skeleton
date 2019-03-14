<?php
/**
 * This file separated that 3rd party application can instantiate this-
 * - and get system folder structure and autoload
 * ! also separate application with pre-startup
 */
use \Poirot\Std\Glob;
use \Poirot\Std\Environment\FactoryEnvironment;

use \Poirot\Config\Reader\Aggregate;
use \Poirot\Config\ResourceFactory;
use \Poirot\Config\Reader\PhpArray;


## Define Unchangeable Consts -----------------------------------------------------------------------------------------|
#
define('DS', DIRECTORY_SEPARATOR);
define('TIME_REQUEST_MICRO', microtime(true));

define('PT_DIR_SKELETON', __DIR__);
define('PT_DIR_CONFIG_INITIAL', __DIR__.DS.'config'); // initial system pre-config unchangable but can overrided


!defined('PT_DIR_ROOT') && define('PT_DIR_ROOT', dirname(__FILE__), false);


## Setup autoLoading:
#
if ( file_exists(__DIR__.'/vendor/autoload.php') )
    require_once __DIR__.'/vendor/autoload.php';

require_once __DIR__.'/vendor/poirot-autoload.php';


## Set environment settings -------------------------------------------------------------------------------------------|
#
// read environment instruction from files
// in order look for: .env | .env.local | .env.[PT_ENV] | .env.[PT_ENV] | and merge data
$aggrConfReader = new Aggregate([]);
$globPattern    = PT_DIR_ROOT.DS.'.env{,.local'.(($env = getenv('PT_ENV')) ? ",.$env,.$env.local" : '').'}{.php}';
foreach ( Glob::glob($globPattern, GLOB_BRACE) as $filePath ) {
    $aggrConfReader->addReader(
        new PhpArray( ResourceFactory::createFromUri($filePath) )
    );
}

// factory environment profile
$envProfile = getenv('PT_ENV_PROFILE') ?: 'default';
$dotEnv     = FactoryEnvironment::of($envProfile, $aggrConfReader);

// apply environment system wide
$dotEnv->apply();

// make it available through app. execution
FactoryEnvironment::setCurrentEnvironment($dotEnv);


## Changeable Consts: (maybe defined through .env) --------------------------------------------------------------------|
#
// by default application folder is in www public
// it can be changed to any other folder like APP_DIR_WWW.'/../app-folder'
!defined('PT_DIR_CONFIG') && define('PT_DIR_CONFIG',       PT_DIR_SKELETON.'/config', false);
!defined('PT_DIR_DATA')   && define('PT_DIR_DATA',         PT_DIR_SKELETON.'/data', false);
!defined('PT_DIR_TMP')    && define('PT_DIR_TMP',              PT_DIR_DATA.'/tmp', false);
!defined('PT_DIR_SOURCE') && define('PT_DIR_SOURCE',       PT_DIR_SKELETON.'/src', false);
!defined('PT_DIR_CORE')   && define('PT_DIR_CORE',             PT_DIR_SOURCE.'/core', false);

!defined('PT_DIR_VENDOR') && define('PT_DIR_VENDOR',       PT_DIR_ROOT.'/vendor', false);
