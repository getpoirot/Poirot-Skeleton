<?php
namespace Application\Actions\Helper;

use Poirot\AaResponder\AbstractAResponder;

// TODO Script/Link Both Extend Something Like ObjectCollection, Reduce Code Clone

class HtmlLinkAction extends AbstractAResponder
{
    /**
     * the link is inserted in the head section.
     */
    protected $links = [];

    /**
     * Allowed attributes
     *
     * @var string[]
     */
    protected $itemKeys = [
        'charset',
        'href',
        'hreflang',
        'id',
        'media',
        'rel',
        'rev',
        'sizes',
        'type',
        'title',
        'extras',
        'itemprop'
    ];

    /**
     * Flag whether to automatically escape output, must also be
     * enforced in the child class if __toString/toString is overridden
     *
     * @var bool
     */
    protected $autoEscape = true;

    /**
     * Invoke HtmlLink
     *
     * @return $this
     */
    function __invoke($arg = null)
    {
        return $this;
    }

    /**
     * Attach Script File
     *
     * @param string    $href   Http Url To File
     * @param array|int $attrs  Attributes Or Priority Offset
     * @param string    $rel    stylesheet
     * @param int|null  $offset Script Priority Offset
     *
     * @return $this
     */
    function attachFile($href, $attrs = [], $rel = 'stylesheet', $offset = null)
    {
        if (is_int($attrs))
            $offset = $attrs;

        if (isset($attrs['type'])) {
            $rel = $attrs['type'];
            unset($attrs['type']);
        }

        $item = [
            'rel'  => $rel,
            'href' => $href,
        ];
        $item = array_merge($item, $attrs);

        $this->__insertScriptStr($this->__itemToString($item), $offset);

        return $this;
    }

    /**
     * Is the link specified a duplicate?
     *
     * - look in all sections
     *
     * @param string $scrStr
     *
     * @return bool
     */
    function hasAttached($scrStr)
    {
        foreach ($this->links as $item) {
            $pattern = '/href=(["\'])(.*?)\1/';
            if (preg_match($pattern, $item, $matches) >= 0)
                if (substr_count($scrStr, $matches[2]) > 0)
                    return true;
        }

        return false;
    }

    /**
     * Render Attached Links
     *
     * @return string
     */
    function __toString()
    {
        return implode('\r\n', $this->links);
    }

    /**
     * Add Script To List
     *
     * @param string  $scrStr
     * @param int     $offset
     */
    protected function __insertScriptStr($scrStr, $offset = null)
    {
        if ($this->hasAttached($scrStr))
            return;

        $this->__insertIntoPosArray($this->links, $scrStr, $offset);
    }

    protected function __insertIntoPosArray(&$array, $element, $offset)
    {
        // [1, 2, x, 4, 5, 6] ---> before [1, 2], after [4, 5, 6]
        $beforeOffsetPart = array_slice($array, 0, $offset);
        $afterOffsetPart  = array_slice($array, $offset);
        # insert element in offset
        $beforeOffsetPart = $beforeOffsetPart + [$offset => $element];
        # glue them back
        $array = array_merge($beforeOffsetPart , $afterOffsetPart);
        arsort($array);
    }

    /**
     * Create HTML link element from data item
     *
     * @param  array $item
     *
     * @return string
     */
    protected function __itemToString(array $item)
    {
        $attributes = $item;
        $link       = '<link';
        foreach ($this->itemKeys as $itemKey) {
            if (isset($attributes[$itemKey])) {
                if (is_array($attributes[$itemKey])) {
                    foreach ($attributes[$itemKey] as $key => $value) {
                        $link .= sprintf(' %s="%s"', $key, ($this->autoEscape) ? addslashes($value) : $value);
                    }
                } else {
                    $link .= sprintf(
                        ' %s="%s"',
                        $itemKey,
                        ($this->autoEscape) ? addslashes($attributes[$itemKey]) : $attributes[$itemKey]
                    );
                }
            }
        }

        $link .= ' />' . PHP_EOL;

        if (($link == '<link />') || ($link == '<link>')) {
            return '';
        }
        if (isset($attributes['conditionalStylesheet'])
            && !empty($attributes['conditionalStylesheet'])
            && is_string($attributes['conditionalStylesheet'])
        ) {
            // inner wrap with comment end and start if !IE
            if (str_replace(' ', '', $attributes['conditionalStylesheet']) === '!IE') {
                $link = '<!-->' . $link . '<!--';
            }
            $link = '<!--[if ' . $attributes['conditionalStylesheet'] . ']>' . $link . '<![endif]-->';
        }

        return $link;
    }
}
