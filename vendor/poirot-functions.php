<?php
namespace Poirot
{
    use Poirot\Application\aSapi;

    const ENV_DIR  = 'directory';
    const ENV_BOOL = 'boolean';
    const ENV_CSV  = 'commaseprated';


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
     * Get Environment Variable
     *
     * @param string     $key
     * @param string     $typeHint
     * @param mixed|null $typeHintOption
     *
     * @return string|bool|array|null
     */
    function getEnv(string $key = null, $typeHint = null)
    {
        $r = null;
        if (array_key_exists($key, $_ENV))
            $r = $_ENV[$key];
        elseif (array_key_exists($key, $_SERVER))
            $r = $_SERVER[$key];

        switch ($typeHint) {
            case ENV_BOOL:
                $r = filter_var($r, FILTER_VALIDATE_BOOLEAN);
                break;
            case ENV_DIR:
                $r = rtrim( $r , '\\/' );
                break;
            case ENV_CSV:
                if (false === $delimiter = func_get_arg(2))
                    $delimiter = ',';

                $r = explode($delimiter, $r);
                break;
        }

        return $r;
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
