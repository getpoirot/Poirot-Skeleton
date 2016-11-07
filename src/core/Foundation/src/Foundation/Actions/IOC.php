<?php
namespace Module\Foundation\Actions;

use Module\Foundation\Actions\Helper\CycleAction;
use Module\Foundation\Actions\Helper\HtmlLinkAction;
use Module\Foundation\Actions\Helper\HtmlScriptAction;
use Module\Foundation\Actions\Helper\PathAction;
use Module\Foundation\Actions\Helper\UrlAction;
use Module\Foundation\Actions\Helper\ViewAction;


/**
 *
 * @method static ViewAction       view($template = null, $variables = null)
 * @method static UrlAction        url($routeName = null, $params = array())
 * @method static PathAction       path($routeName = null, $params = array())
 * @method static HtmlScriptAction htmlScript($section = 'inline')
 * @method static HtmlLinkAction   htmlLink()
 * @method static CycleAction      cycle($action = null, $steps = 1, $reset = true)
 */
class IOC extends \IOC
{ }
