<?php

namespace Miniphy\Drivers\Html;

use Miniphy\Drivers\AbstractDriver;
use Miniphy\Miniphy;

abstract class AbstractHtmlDriver extends AbstractDriver
{
    /**
     * The following elements are generally considered inline in HTML. Sourced from Mozilla documentation:
     * https://developer.mozilla.org/en-US/docs/Web/HTML/Inline_elements
     *
     * @var array
     */
    protected $inlineElements = [
        'a', 'b', 'big', 'i', 'small', 'tt', 'abbr', 'acronym', 'cite', 'code', 'dfn', 'em', 'kbd', 'strong', 'samp',
        'time', 'var', 'bdo', 'br', 'img', 'map', 'object', 'q', 'script', 'span', 'sub', 'sup', 'button', 'input',
        'label', 'select', 'textarea'
    ];

    /**
     * The following elements are generally considered block-level in HTML. Sourced from Mozilla documentation:
     * https://developer.mozilla.org/en-US/docs/Web/HTML/Block-level_elements
     *
     * @var array
     */
    protected $blockElements = [
        'address', 'article', 'aside', 'blockquote', 'br', 'canvas', 'dd', 'div', 'dl', 'fieldset', 'figcaption',
        'figure', 'footer', 'form', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'header', 'hgroup', 'hr', 'li', 'main', 'nav',
        'noscript', 'ol', 'output', 'p', 'pre', 'section', 'table', 'tfoot', 'ul', 'video'
    ];

    /**
     * AbstractHtmlDriver constructor.
     *
     * @param \Miniphy\Miniphy $miniphy
     */
    public function __construct(Miniphy $miniphy)
    {
        parent::__construct($miniphy);
    }

    /**
     * Get the set of inline elements.
     *
     * @return array
     */
    public function getInlineElements()
    {
        return $this->inlineElements;
    }

    /**
     * Tell if the provided tag is inline by default.
     *
     * @param string $tag
     *
     * @return bool
     */
    protected function isInline($tag)
    {
        return in_array($tag, $this->inlineElements);
    }

    /**
     * Get the set of block elements.
     *
     * @return array
     */
    public function getBlockElements()
    {
        return $this->blockElements;
    }

    /**
     * Tell if the provided tag is block by default.
     *
     * @param string $tag
     *
     * @return bool
     */
    protected function isBlock($tag)
    {
        return in_array($tag, $this->blockElements);
    }

    /**
     * Get the current mode.
     *
     * @return int
     */
    public function getMode()
    {
        return $this->miniphy->getHtmlMode();
    }
}
