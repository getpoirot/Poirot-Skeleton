<?php
namespace Module\Foundation\Actions
{
    use Module\Foundation\Actions\Helper\CycleAction;
    use Module\Foundation\Actions\Helper\FlashMessageAction;
    use Module\Foundation\Actions\Helper\HtmlLinkAction;
    use Module\Foundation\Actions\Helper\HtmlScriptAction;
    use Module\Foundation\Actions\Helper\UrlAction;
    use Module\Foundation\Actions\Helper\ViewAction;
    use Module\Foundation\Services\PathService\PathAction;

    /**
     *
     * @method static ViewAction         view($template = null, $variables = null)
     * @method static UrlAction          url($routeName = null, $params = array())
     * @method static PathAction         path($pathString = null, $variables = array())
     * @method static FlashMessageAction flashMessage($messageNamespace = 'info')
     * @method static HtmlScriptAction   htmlScript($section = 'inline')
     * @method static HtmlLinkAction     htmlLink()
     * @method static CycleAction        cycle($action = null, $steps = 1, $reset = true)
     */
    class IOC extends \IOC
    { }
}


namespace Module\Foundation\Services
{
    use Module\Foundation\Services\PathService\PathAction;

    /**
     * @method static PathAction Path()
     */
    class IOC extends \IOC
    { }
}
