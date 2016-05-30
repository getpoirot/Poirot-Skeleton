<?php
namespace Module\Foundation\Actions\Helper;

use Module\Foundation\Actions\aAction;

// TODO Script/Link Both Extend Something Like ObjectCollection, Reduce Code Clone

class HtmlScriptAction 
    extends aAction
{
    /** @var string Current Script Section */
    protected $_currSection;

    /** @var array Attached Scripts */
    protected $scripts = array(
        // 'section' => [],
    );

    /**
     * Flag whether to automatically escape output, must also be
     * enforced in the child class if __toString/toString is overridden
     *
     * @var bool
     */
    protected $autoEscape = true;

    /**
     * Are arbitrary attributes allowed?
     *
     * @var bool
     */
    protected $_arbitraryAttributes = false;

    /**
     * Optional allowed attributes for script tag
     *
     * @var array
     */
    protected $optionalAttributes = array(
        'charset',
        'crossorigin',
        'defer',
        'language',
        'src',
    );

    /**
     * Invoke HtmlScript
     *
     * @param string $section
     *
     * @return $this
     */
    function __invoke($section = 'inline')
    {
        $this->_currSection = (string) $section;
        return $this;
    }

    /**
     * Attach Script File
     *
     * @param string    $src    Http Url To File
     * @param array|int $attrs  Attributes Or Priority Offset
     * @param string    $type   Text/Javascript
     * @param int|null  $offset Script Priority Offset
     *
     * @return $this
     */
    function attachFile($src, $attrs = array(), $type = 'text/javascript', $offset = null)
    {
        if (is_int($attrs))
            $offset = $attrs;

        if (isset($attrs['type'])) {
            $type = $attrs['type'];
            unset($attrs['type']);
        }

        $item = array(
            "type"       => $type,
            "attributes" => array_merge(array("src" => (string) $src), $attrs)
        );

        $this->_insertScriptStr($this->_itemToString($item), $offset);

        return $this;
    }

    /**
     * Attach Script Content
     *
     * @param string    $script Script Content
     * @param array|int $attrs  Attributes Or Priority Offset
     * @param string    $type   Text/Javascript
     * @param int|null  $offset Script Priority Offset
     *
     * @return $this
     */
    function attachScript($script, $attrs = array(), $type = 'text/javascript', $offset = null)
    {
        if (isset($attrs['type'])) {
            $type = $attrs['type'];
            unset($attrs['type']);
        }

        $item = array(
            "source"     => (string) $script,
            "type"       => $type,
            "attributes" => $attrs
        );

        $this->_insertScriptStr($this->_itemToString($item), $offset);

        return $this;
    }

    /**
     * Is the script specified a duplicate?
     *
     * - look in all sections
     *
     * @param string $scrStr
     *
     * @return bool
     */
    function hasAttached($scrStr)
    {
        $duplicate = false;
        foreach(array_keys($this->scripts) as $section) {
            $duplicate |= $this->_hasAttached__equalSrc($section, $scrStr);

            if ($duplicate)
                break;
        }

        return $duplicate;
    }

    /**
     * Render Attached Scripts
     *
     * @return string
     */
    function __toString()
    {
        $scripts = (isset($this->scripts[$this->_currSection]))
            ? $this->scripts[$this->_currSection]
            : array();

        return implode('\r\n', $scripts);
    }

    /**
     * Add Script To List
     *
     * @param string  $scrStr
     * @param int     $offset
     */
    protected function _insertScriptStr($scrStr, $offset = null)
    {
        if ($this->hasAttached($scrStr))
            return;


        if (!array_key_exists($this->_currSection, $this->scripts))
            $this->scripts[$this->_currSection] = array();

        $this->_insertIntoPosArray($this->scripts[$this->_currSection], $scrStr, $offset);
    }

    protected function _insertIntoPosArray(&$array, $element, $offset)
    {
        // [1, 2, x, 4, 5, 6] ---> before [1, 2], after [4, 5, 6]
        $beforeOffsetPart = array_slice($array, 0, $offset);
        $afterOffsetPart  = array_slice($array, $offset);
        # insert element in offset
        $beforeOffsetPart = $beforeOffsetPart + array($offset => $element);
        # glue them back
        $array = array_merge($beforeOffsetPart , $afterOffsetPart);
        arsort($array);
    }

    /**
     * Convert Script Array Representation To String
     *
     * @param array        $item Script Array Representation
     * @param $indent
     * @param $escapeStart
     * @param $escapeEnd
     *
     * @return string
     */
    protected function _itemToString(array $item, $indent = '', $escapeStart = '', $escapeEnd = '')
    {
        $item = (object) $item;

        $attrString = '';
        if (!empty($item->attributes)) {
            foreach ($item->attributes as $key => $value) {
                if ((!$this->_isArbitraryAttributesAllowed() && !in_array($key, $this->optionalAttributes))
                    || in_array($key, array('conditional', 'noescape'))
                )
                    continue;

                if ('defer' == $key)
                    $value = 'defer';

                $attrString .= sprintf(' %s="%s"', $key, ($this->autoEscape) ? addslashes($value) : $value);
            }
        }

        $addScriptEscape = !(isset($item->attributes['noescape'])
            && filter_var($item->attributes['noescape'], FILTER_VALIDATE_BOOLEAN));

        $type = ($this->autoEscape) ? addslashes($item->type) : $item->type;
        $html = '<script type="' . $type . '"' . $attrString . '>';

        if (!empty($item->source)) {
            $html .= PHP_EOL;

            if ($addScriptEscape)
                $html .= $indent . '    ' . $escapeStart . PHP_EOL;

            $html .= $indent . '    ' . $item->source;

            if ($addScriptEscape)
                $html .= PHP_EOL . $indent . '    ' . $escapeEnd;

            $html .= PHP_EOL . $indent;
        }

        $html .= '</script>';

        if (isset($item->attributes['conditional'])
            && !empty($item->attributes['conditional'])
            && is_string($item->attributes['conditional'])
        ) {
            // inner wrap with comment end and start if !IE
            if (str_replace(' ', '', $item->attributes['conditional']) === '!IE')
                $html = '<!-->' . $html . '<!--';

            $html = $indent . '<!--[if ' . $item->attributes['conditional'] . ']>' . $html . '<![endif]-->';
        } else
            $html = $indent . $html;

        return $html;
    }

    /**
     * !! Override code
     *
     * Are arbitrary attributes allowed?
     *
     * @return bool
     */
    protected function _isArbitraryAttributesAllowed()
    {
        return $this->_arbitraryAttributes;
    }

    /**
     * Find duplicate scripts in sections by src
     *
     * @param $section
     * @param $scrStr
     *
     * @return bool
     */
    protected function _hasAttached__equalSrc($section, $scrStr)
    {
        foreach ($section as $item) {
            $pattern = '/src=(["\'])(.*?)\1/';

            if(preg_match($pattern, $item, $matches) >= 0)
                if (substr_count($scrStr, $matches[2]) > 0)
                    return true;
        }

        return false;
    }
}
