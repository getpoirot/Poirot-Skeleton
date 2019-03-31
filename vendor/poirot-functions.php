<?php
namespace Poirot
{
    use Poirot\Application\aSapi;

    if (! function_exists('config')) {
        /**
         * Get Config Values
         *
         * Argument can passed and map to config if exists [$key][$_][$__] ..
         *
         * @param $key
         * @param null $_
         *
         * @return mixed|null
         * @throws \Exception
         */
        function config($key = null, $_ = null)
        {
            /** @var aSapi $config */
            $app = \IOC::GetIoC()->get('/sapi');

            $config = $app->config();
            foreach (func_get_args() as $key) {
                if (! isset($config[$key]) )
                    return null;

                $config = $config[$key];
            }

            return $config;
        }
    }

    /**
     * Is Sapi Command Line?
     *
     * @return bool
     */
    function isCommandLine($sapiName = null)
    {
        if ($sapiName === null)
            $sapiName = php_sapi_name();

        return ( strpos($sapiName, 'cli') === 0 );
    }
}

namespace Poirot\Config
{
    use Poirot\Skeleton\Config;
    use Poirot\Std\Type\StdArray;


    /**
     * Load Config Files From Given Directory
     *
     * @param string $path file or dir path
     *
     * @return StdArray|false
     */
    function load(string $path)
    {
        $configLoader = new Config($path);
        return $configLoader->load();
    }
}
