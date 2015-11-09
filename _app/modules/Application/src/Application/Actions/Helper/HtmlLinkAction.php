<?php
namespace Application\Actions\Helper;

use Poirot\AaResponder\AbstractAResponder;

class HtmlLinkAction extends AbstractAResponder
{

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
     * Registry key for placeholder
     *
     * @var string
     */
    protected $regKey = 'Zend_View_Helper_HeadLink';

    /**
     * !! Override code
     *
     * Flag whether to automatically escape output, must also be
     * enforced in the child class if __toString/toString is overridden
     *
     * @var bool
     */
    protected $autoEscape = true;


    /**
     * the link is inserted in the head section.
     */
    protected $links = [];

    function __invoke($arg =null)
    {

        return $this;
    }

    public function appendFile($href, $attrs = array(), $rel = 'stylesheet')
    {
        $item = array(
            "rel" => $rel,
            "href" => $href
        );
        $item = array_merge($item, $attrs);
        $this->addObject($this->itemToString($item));

        return $this;
    }

    public function offsetSetFile($index, $href, $attrs = array(), $rel = 'stylesheet')
    {
        $item = array(
            "rel" => $rel,
            "href" => $href
        );
        $item = array_merge($item, $attrs);
        $this->addObject($this->itemToString($item), $index);

        return $this;
    }

    public function addObject($object, $index = null)
    {
        if(!$this->isDuplicate($object)){
                    $this->insertArrayIndex($this->links, $object, $index);
        }
    }

    function insertArrayIndex(&$array, $new_element, $index)
    {
            /*** get the start of the array ***/
            $start = array_slice($array, 0, $index);
            /*** get the end of the array ***/
            $end = array_slice($array, $index);
            /*** add the new element to the array ***/
            $start = $start + array($index => $new_element);
            /*** glue them back together and return ***/
            $array = array_merge($start , $end);
         arsort($array);

        return $array;
    }

    /**
     * !! Override code
     * Create HTML link element from data item
     *
     * @param  \stdClass $item
     * @return string
     */
    public function itemToString($item)
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


    /**
     * Is the file specified a duplicate?
     * @param $file
     * @return bool
     */
    protected function isDuplicate($file)
    {
        foreach ($this->links as $item) {
            $pattern = '/href=(["\'])(.*?)\1/';
            if(preg_match($pattern, $item, $matches)>=0){
                if (substr_count($file, $matches[2])>0) {
                    return true;
                }
            }
        }

        return false;
    }

    function __toString()
    {
        return implode(',', $this->links);
    }
}