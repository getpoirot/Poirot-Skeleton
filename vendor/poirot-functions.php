<?php
namespace
{
    use Poirot\Ioc\Container;

    /**
     * Class IOC
     *
     * Helper To Ease Access To Ioc Services by extend this:
     *   - on static call use extend class namespace to achieve nested container
     *     and retrieve service from that container.
     *
     *   $directory = \Module\Categories\Services\Repository\IOC::categories();
     *   $r = $directory->getTree($directory->findByID('red'));
     */
    class IOC
    {
        static function __callStatic($name, $arguments)
        {
            $class     = get_class(new static);
            $namespace = substr($class, 0, strrpos($class, '\\'));
            $nested    = str_replace('\\', Container::SEPARATOR, $namespace);
            $container = self::_ioc()->from($nested);

            if (!$container)
                throw new \Exception(sprintf('Nested Container (%s) not included.', $nested));

            if ($arguments)
                $service = $container->get($name, $arguments);
            else 
                $service = $container->get($name);
            
            return $service;
        }

        /** @return Container */
        protected static function _ioc()
        {
            return \Poirot\IoC();
        }
    }
}

namespace Poirot
{
    use Poirot\Ioc\Container;
    use Poirot\View\ViewModel\RendererPhp;

    /**
     * IoC Container Gateway
     *
     * // TODO implement namespace based service resolver for root container
     * 
     * @return Container
     */
    function IoC() {
        static $IoC;
        if ($IoC) return $IoC;

        $config = \Poirot\Config\load(PT_DIR_CONFIG.'/services');
        $IoC    = new Container(new Container\BuildContainer($config));
        return $IoC;
    };

    /**
     * Is Sapi Command Line?
     *
     * @return bool
     */
    function isCommandLine()
    {
        return ( strpos(php_sapi_name(), 'cli') === 0 );
    }

    /**
     * Print Exception Object Error Page
     *
     * @param \Exception|\Throwable $e
     *
     * @throws \Throwable
     */
    function printException($e) {
        if (ob_get_level())
            ## clean output buffer, display just error page
            ob_end_clean();
        try {
            $renderer = new RendererPhp();
            echo $renderer->capture(
                PT_DIR_THEME_DEFAULT.'/error/general.php'
                , array('exception' => $e)
            );
        } catch(\Exception $ve) {
            ## throw exception if can't render template
            throw $e;
        }
    }
}

namespace Poirot\Config
{

    use Poirot\Ioc\Container;
    use Poirot\Std\ErrorStack;
    use Poirot\Std\Type\StdArray;

    /**
     * Load Config Files From Given Directory
     *
     * - file name can be in form of dir/to/file then:
     *   it will looking for files
     *   "file.local.conf.php" and "file.conf.php"
     *
     * - if given argument is directory:
     *   load all files with extension
     *   ".local.conf.php" and ".conf.php"
     *
     * @param string $dirOrFile file or dir path
     *
     * @return StdArray
     */
    function load($dirOrFile)
    {
        $config = new StdArray();
        
        $globPattern = $dirOrFile;
        if (is_dir($dirOrFile)) {
            $globPattern = str_replace('\\', '/', $globPattern); // normalize path separator
            $globPattern = rtrim($globPattern, '/').'/*';
        }
        if (!is_file($dirOrFile))
            $globPattern .= '.{,local.}conf.php';

        foreach (glob($globPattern, GLOB_BRACE) as $filePath) {
            ErrorStack::handleException(function ($e) use ($filePath) {
                ob_end_clean();
                throw new \RuntimeException(
                    sprintf('Error while loading config: %s', $filePath)
                    , $e
                );
            });

            ErrorStack::handleError(E_ALL, function ($e){ throw $e; });

            ob_start();
            $fConf = include_once $filePath;
            if (!is_array($fConf)) throw new \RuntimeException('Config file must provide array.');
            $config = $config->withMergeRecursive($fConf);
            ob_get_clean();

            ErrorStack::handleDone();
            ErrorStack::handleDone();
        }

        return $config;
    }

    /**
     * Instantiate Initialized From Config Data.
     *
     * if services(ioc) not given using default Poirot\ioc() then:
     *   - make object instance from definition data structure
     *   - inject dependencies
     *   - initialize services
     *
     * @param array|\Traversable $config
     * @param null|Container     $services
     *
     * @return array Config replaced with initialized services
     */
    function instanceInitialized($config, $services = null)
    {
        /*
        'identifier' => array(
            // [X] This will convert into Identifier instance [ 'identifier' => ObjectInstance ]
            '_class_'   => [
                '\Poirot\AuthSystem\Authenticate\Identifier\IdentifierHttpBasicAuth',
                'options' => array(
                    #O# adapter => iIdentityCredentialRepo | (array) options of CredentialRepo
                    'credential_adapter' => array(
                        // [X] This will convert into instance [ 'credential_adapter' => ObjectInstance ]
                        '_class_'   => [
                            '\Poirot\AuthSystem\Authenticate\RepoIdentityCredential\IdentityCredentialDigestFile',
                            'options' => array(
                                'pwd_file_path' => __DIR__.'/../data/users.pws',
                            ),
                        ],
                    )
                ),
            ],
        ),
        */
        
        if ($config instanceof \Traversable)
            $config = \Poirot\Std\cast($config)->toArray();

        if (!is_array($config))
            throw new \InvalidArgumentException(sprintf(
                'Config must be Array Or Traversable; given: (%s).'
                , \Poirot\Std\flatten($config)
            ));

        if ($services === null)
            // using default container to initialize instances
            $services = \Poirot\IoC();

        if (!$services instanceof Container)
            throw new \InvalidArgumentException(sprintf(
                'Services must instance of Container; given: (%s).'
                , \Poirot\Std\flatten($services)
            ));
        
        
        $services = clone $services;

        foreach ($config as $key => $value)
        {
            if ($key === '_class_')
            {
                // instance object from _class_ config definition
                // 'key' => [ '_class_' => '\ClassName' | ['\ClassName', 'options' => $options] ]

                if (is_string($value))
                    $value = array($value);

                // Maybe Options Contains Must Initialized Definition
                $value = instanceInitialized($value, $services);

                $service_name = uniqid();
                $class        = array_shift($value);
                $inService    = new Container\Service\ServiceInstance();
                $inService->setName($service_name);
                $inService->setService($class);
                $inService->optsData()->import($value);

                $services->set($inService);
                $initialized = $services->get($service_name);
                unset($config[$key]);
                if (empty($config))
                    // only definition structure and will convert to instance only
                    $config = $initialized;
                else
                    array_unshift($config, $initialized);
            }
            elseif (is_array($value)) 
            {
                $config[$key] = instanceInitialized($value, $services);
            }
        }

        return $config;
    }
}
