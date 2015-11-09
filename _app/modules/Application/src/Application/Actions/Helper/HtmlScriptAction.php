<?php
namespace Application\Actions\Helper;

use Poirot\AaResponder\AbstractAResponder;

class HtmlScriptAction extends AbstractAResponder
{

    protected $currentPos;

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
     * !! Override code
     *
     * Are arbitrary attributes allowed?
     *
     * @var bool
     */
    protected $arbitraryAttributes = false;

    /**
     * !! Override code
     *
     * Optional allowed attributes for script tag
     *
     * @var array
     */
    protected $optionalAttributes = [
        'charset',
        'crossorigin',
        'defer',
        'language',
        'src',
    ];

    /**
     * the script is inserted in the head section right before the title element.
     */
    const POS_HEAD = 0;
    protected $headPos = [];

    /**
     *  the script is inserted at the beginning of the body section.
     */
    const POS_BEGIN = 1;
    protected $beginPos = [];

    /**
     * the script is inserted at the end of the body section.
     */
    const POS_END = 2;
    protected $endPos = [];


    function __invoke($pos = null)
    {
        if ($pos === $this::POS_BEGIN || $pos === $this::POS_END || $pos === $this::POS_HEAD) {
            $this->currentPos = $pos;
        }
        return $this;
    }

    public function appendFile($src, $type = 'text/javascript', $attrs = array())
    {
        $item = array(
            "type" => $type,
            "attributes" => array_merge(array("src" => $src), $attrs)
        );

        $this->addObject($this->itemToString((object)$item, "", "", ""));

    }

    public function offsetSetFile($index, $src, $type = 'text/javascript', $attrs = array())
    {
        $item = array(
            "type" => $type,
            "attributes" => array_merge(array("src" => $src), $attrs)
        );
        $this->addObject($this->itemToString((object)$item, "", "", ""), $index);
    }

    public function appendScript($script, $type = 'text/javascript', $attrs = array())
    {
        $item = array(
            "source" => $script,
            "type" => $type,
            "attributes" => $attrs
        );

        $this->addObject($this->itemToString((object)$item, "", "", ""));

    }

    public function offsetSetScript($index, $script, $type = 'text/javascript', $attrs = array())
    {
        $item = array(
            "source" => $script,
            "type" => $type,
            "attributes" => $attrs
        );

        $this->addObject($this->itemToString((object)$item, "", "", ""), $index);

    }

    public function addObject($object, $index = null)
    {
        if(!$this->isDuplicate($object)){
            switch ($this->currentPos) {
                case $this::POS_HEAD:
                default:
                    $this->insertArrayIndex($this->headPos, $object, $index);
                    break;
                case $this::POS_BEGIN:
                    $this->insertArrayIndex($this->beginPos, $object, $index);
                    break;
                case $this::POS_END:
                    $this->insertArrayIndex($this->endPos, $object, $index);
                    break;
            }
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
     *
     * @param $item
     * @param $indent
     * @param $escapeStart
     * @param $escapeEnd
     * @return string
     */
    public function itemToString($item, $indent, $escapeStart, $escapeEnd)
    {
        $attrString = '';

        if (!empty($item->attributes)) {
            foreach ($item->attributes as $key => $value) {
                if ((!$this->arbitraryAttributesAllowed() && !in_array($key, $this->optionalAttributes))
                    || in_array($key, ['conditional', 'noescape'])
                ) {
                    continue;
                }
                if ('defer' == $key) {
                    $value = 'defer';
                }
                $attrString .= sprintf(' %s="%s"', $key, ($this->autoEscape) ? addslashes($value) : $value);
            }
        }

        $addScriptEscape = !(isset($item->attributes['noescape'])
            && filter_var($item->attributes['noescape'], FILTER_VALIDATE_BOOLEAN));

        $type = ($this->autoEscape) ? addslashes($item->type) : $item->type;
        $html = '<script type="' . $type . '"' . $attrString . '>';

        if (!empty($item->source)) {
            $html .= PHP_EOL;

            if ($addScriptEscape) {
                $html .= $indent . '    ' . $escapeStart . PHP_EOL;
            }

            $html .= $indent . '    ' . $item->source;

            if ($addScriptEscape) {
                $html .= PHP_EOL . $indent . '    ' . $escapeEnd;
            }

            $html .= PHP_EOL . $indent;
        }

        $html .= '</script>';

        if (isset($item->attributes['conditional'])
            && !empty($item->attributes['conditional'])
            && is_string($item->attributes['conditional'])
        ) {
            // inner wrap with comment end and start if !IE
            if (str_replace(' ', '', $item->attributes['conditional']) === '!IE') {
                $html = '<!-->' . $html . '<!--';
            }
            $html = $indent . '<!--[if ' . $item->attributes['conditional'] . ']>' . $html . '<![endif]-->';
        } else {
            $html = $indent . $html;
        }

        return $html;
    }

    /**
     * !! Override code
     *
     * Are arbitrary attributes allowed?
     *
     * @return bool
     */
    protected function arbitraryAttributesAllowed()
    {
        return $this->arbitraryAttributes;
    }


    /**
     * Is the file specified a duplicate?
     * @param $file
     * @return bool
     */
    protected function isDuplicate($file)
    {
        if($this->equalSrc($this->headPos, $file))
            return true;
        if($this->equalSrc($this->beginPos, $file))
            return true;
        if($this->equalSrc($this->endPos, $file))
            return true;
        return false;
    }

    /**
     *  Help to find duplicate file in 3 sections
     * @param $scriptArray
     * @param $file
     * @return bool
     */
    protected function equalSrc(&$scriptArray, $file){
        foreach ($scriptArray as $item) {
            $pattern = '/src=(["\'])(.*?)\1/';
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

        switch ($this->currentPos) {
            case $this::POS_HEAD:
            default:
                return implode(' ', $this->headPos);
                break;
            case $this::POS_BEGIN:
                return implode(' ', $this->beginPos);
                break;
            case $this::POS_END:
                return implode(' ', $this->endPos);
                break;
        }
        return implode(',', $this->scripts);
    }
}