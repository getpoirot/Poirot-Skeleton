<?php
/** 
---------------------------------------------------------------------------------------------------
 Configuration Environment Variables:

   -----
   DEBUG converted to constant DEBUG when get it.
   then: if set to true try to load "dev" from .env factory
 
 
   --------------
   PT_ENV_PROFILE factory environment profile with name string
   equal to this value otherwise "default" (server_default) used 
   
   @see \Poirot\Std\Environment\FactoryEnvironment for more possible options

 
   ------
   PT_ENV override .env.xxx with specification equavalent to this value
   in order look for: .env | .env.local | .env.[PT_ENV] | .env.[PT_ENV] | and merge data

*/
namespace Poirot
{
    use Poirot as P;

    define('FALLBACK_REQUIRED_POIROT', true);
    $application = include_once __DIR__.'/index.require_fallback.php';

    // change cwd to the application root by default
    chdir(PT_DIR_ROOT);

    // Run the application:
    /** @var P\Application\Sapi $application */
    $application->run();
}
