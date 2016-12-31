<?php
namespace Module\Foundation\Actions\Helper;

class HtmlHeadTitle
{
    protected $separator = '::';
    protected $title = array();


    /**
     * Invoke HtmlScript
     *
     * @param string|null $title
     *
     * @return $this
     */
    function __invoke($title = null)
    {
        if ($title !== null)
            $this->appendTitle($title);

        return $this;
    }

    /**
     * Set Path Separator
     * @param string $separator
     * @return $this
     *
     */
    function setSeparator($separator)
    {
        $this->separator = (string) $separator;
        return $this;
    }

    /**
     * Get Path Separator
     * @return string
     */
    function getSeparator()
    {
        return $this->separator;
    }

    /**
     * Append Title
     * @param string $title
     * @return $this
     */
    function appendTitle($title)
    {
        $title = (string) $title;
        if (current($this->title) == $title)
            return $this;

        $this->title[] = (string) $title;
        return $this;
    }

    /**
     * Get Title
     * !! without title html tag
     * @return string
     */
    function getTitle()
    {
        return implode($this->getSeparator(), $this->title);
    }

    /**
     * Render Attached Scripts
     *
     * @return string
     */
    function __toString()
    {
        return sprintf('<title>%s</title>', $this->getTitle());
    }
}
