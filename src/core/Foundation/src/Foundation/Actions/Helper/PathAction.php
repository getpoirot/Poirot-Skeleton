<?php
namespace Module\Foundation\Actions\Helper;

use Module\Foundation\Actions\aAction;
use Poirot\Std\ConfigurableSetter;
use Poirot\Std\Struct\DataEntity;

class PathAction 
    extends ConfigurableSetter
{
    /** @var array key value of paths name and uri */
    protected $paths = array(
        ## name => '$var/uri/to/path'
    );

    /** @var array Path names Restricted from override */
    protected $__restricted = array(
        # 'path-name' => true
    );

    /** @var string Last invoked path name */
    protected $__lastInvokedPath;
    /** @var string Last invoked uri */
    protected $__lastInvokedUri;
    
    /** @var DataEntity */
    protected $params;

    /**
     * @inheritdoc
     *
     * - path('path-name')
     * - path('$user/path/uri')
     *
     * - path($uriOrName, ['var' => 'value'])
     * @see PathAction::_assemble
     *
     * @param null $arg
     *
     * @return $this
     */
    function __invoke()
    {
        $funcArgs = func_get_args();

        if (empty($funcArgs))
            ## path()
            return $this;


        // attain uri:

        ## path($name, ..)
        $name = array_shift($funcArgs);

        if ($this->hasPath($name))
            $uri = $this->getPath($name);
        else
            ## we don't have pathName, assume that entered text is uri
            $uri = $name;


        // assemble uri with given arguments as variables:

        array_unshift($funcArgs, $uri); ### we want uri as first argument
        ## assemble($uri, ..arguments)
        $assembledUri = call_user_func_array(
            array($this, '_assemble')
            , $funcArgs
        );

        $assembledUri = rtrim($assembledUri, '/');

        $this->__lastInvokedUri  = $assembledUri;
        $this->__lastInvokedPath = $name;

        return $this;
    }

    /**
     * Set key/value pair of paths and Uri
     *
     * @param array $paths
     *
     * @throws \Exception
     * @return $this
     */
    function setPaths(array $paths)
    {
        if (!empty($paths) && array_values($paths) == $paths)
            throw new \Exception('Paths Must Be Associated Array.');

        foreach ($paths as $name => $uri)
            $this->setPath($name, $uri);

        return $this;
    }

    /**
     * Set path uri alias
     *
     * @param string $name
     * @param string $uri
     * @param bool   $isRestricted
     *
     * @throws \Exception
     * @return $this
     */
    function setPath($name, $uri, $isRestricted = false)
    {
        if ($this->hasPath($name) && $this->_isRestricted($name))
            throw new \Exception(
                sprintf('Path with name (%s) already exists and not allow override it.', $name)
            );

        $n = $this->_normalizeName($name);
        $this->paths[$n] = (string) $uri;
        (!$isRestricted) ?: $this->__restricted[$n] = true;

        return $this;
    }

    /**
     * Get pathName uri
     *
     * @param string $name
     *
     * @return mixed
     * @throws \Exception
     */
    function getPath($name)
    {
        if (!$this->hasPath($name))
            throw new \Exception(sprintf('Path with name (%s) not found.', $name));

        $n = $this->_normalizeName($name);
        return $this->paths[$n];
    }

    /**
     * Determine that pathname is exists?
     *
     * @param $name
     *
     * @return bool
     */
    function hasPath($name)
    {
        $n = $this->_normalizeName($name);
        return isset($this->paths[$n]);
    }

    /**
     * Get All Registered Paths
     *
     * @return array
     */
    function getPaths()
    {
        return $this->paths;
    }

    function params()
    {
        if (!$this->params)
            $this->params = new DataEntity;
        
        return $this->params;
    }


    // ...

    /**
     * Get Last Invoked Uri
     *
     * !! echo path()
     *
     * @return string
     */
    function __toString()
    {
        return $this->__lastInvokedUri;
    }

    /**
     * Assemble Uri
     *
     * ! the uri is a string including variable names
     *
     * usage:
     * $uri = '/path/to/:variable'
     * - assembleUri($uri);                          # using default class variables
     * - assembleUri($uri, ['variable' => 'value']); # replace default class variables
     *
     * - variable can be a valid callable
     *   ['variable' => function() { return 'fetched-value'; }]
     *
     * @param string $uri  Uri or Registered Path
     * @param array  $vars
     *
     * @throws \Exception
     * @return mixed
     */
    protected function _assemble($uri, array $vars = array())
    {
        if (!empty($vars) && array_values($vars) == $vars)
            throw new \Exception('Variable Arrays Must Be Associated.');

        /**
         * $matches[0] retrun array of full variables matched, exp. $path
         * $matches[1] retrun array of variables name matched, exp. path
         */
        preg_match_all('/\$(\w[\w\d]*)/', $uri, $matches);

        if (count($matches[0]) === 0)
            // we don't have any variable in uri
            return $uri;

        $vars = array_merge(\Poirot\Std\cast($this->params())->toArray(), $vars);

        // correct order of variables
        // 'path' => 'ValuablePath' TO 0 => 'ValuablePath'
        foreach ($matches[1] as $i => $var) {
            if (! array_key_exists($var, $vars))
                throw new \Exception(sprintf(
                    'Value of variable (%s) is not defined.', $var
                ));

            $currValue = $vars[$var];
            if ($currValue instanceof \Closure)
                $currValue = $currValue();

            $vars[$i]  = $currValue;
            unset($vars[$var]);
        }

        // replace variables to uri
        foreach ($matches[0] as $i => $inUriVar) {
            $uri = preg_replace('/\\'.$inUriVar.'/', $vars[$i], $uri, 1);
        }

        return $uri;
    }

    /**
     * Check that given path name is restricted from override
     * @param $name
     * @return bool
     */
    protected function _isRestricted($name)
    {
        $name = $this->_normalizeName($name);
        return isset($this->__restricted[$name]);
    }

    /**
     * Normalize names
     *
     * @param $name
     *
     * @return string
     */
    protected function _normalizeName($name)
    {
        return strtolower((string) $name);
    }
}
