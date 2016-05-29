<?php
namespace Module\Foundation;

trait tActionComplex
{
    /** Default namespace storing default application actions */
    public static $APP_ACTIONS_DEFAULT = '/module/application';

    protected $_cache__InvokablePlugins = [
        # 'namespace' => InvokablePlugins
    ];
    /** @var PluginsInvokable */
    protected $__cache_callProxyInvokable;

    /** @var Container */
    protected $_t__sContainer;

    /**
     * Invokable Actions
     *
     * - get service from sContainer with __call support
     *   $sContainer->get('service')->__invoke([$args]) ---> $sContainer->service('arg1', 'arg2')
     *
     * note: /module/application[.action]
     *       .action is optional
     *
     * @param string $namespacePath
     *
     * @return PluginsInvokable
     */
    function action($namespacePath = null)
    {
        ### using default namespace value
        ($namespacePath !== null) ?: $namespacePath = self::$APP_ACTIONS_DEFAULT;

        if (isset($this->_cache__InvokablePlugins[$namespacePath]))
            return $this->_cache__InvokablePlugins[$namespacePath];

        $services = $this->services();
        $actions  = self::$APP_ACTIONS_DEFAULT;

        ## /module/application.action
        if (substr_count($namespacePath, '/', 0)) {
            $explode  = explode('/', $namespacePath);
            $actions  = array_pop($explode);

            $services = $services->from(implode('/', $explode));
        }

        if (!strstr($actions, '.action'))
            $actions .= '.action';

        $aContainer = $services->with($actions);
        if ($aContainer === false)
            throw new \InvalidArgumentException(sprintf(
                'Actions Container (%s) not found.'
                , $actions
            ));

        return $this->_cache__InvokablePlugins[$namespacePath] = new PluginsInvokable($aContainer);
    }


    // Implement iCService

    /**
     * Get Module Actions Container
     *
     * - this actions also is member of Module Actions Container
     *
     * @return Container
     */
    function services()
    {
        return $this->_t__sContainer;
    }

    /**
     * Set Service Container
     *
     * - module actions container injected by initializers
     *
     * @param iContainer $container
     */
    function setServiceContainer(iContainer $container)
    {
        $this->_t__sContainer = $container;
    }
}
