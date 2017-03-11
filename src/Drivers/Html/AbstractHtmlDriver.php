<?php

namespace Miniphy\Drivers\Html;

use InvalidArgumentException;
use Miniphy\Drivers\AbstractDriver;
use Miniphy\Miniphy;

abstract class AbstractHtmlDriver extends AbstractDriver
{
    const MODE_SOFT = 1;
    const MODE_MEDIUM = 2;
    const MODE_HARD = 3;

    /**
     * The default minification mode.
     *
     * @var int
     */
    protected $mode;

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
     * @param int|null         $mode
     */
    public function __construct(Miniphy $miniphy, $mode = null)
    {
        parent::__construct($miniphy);

        $this->setMode(!is_null($mode) ? $mode : static::MODE_SOFT);
    }

    /**
     * Set the mode. The current instance is returned for chaining.
     *
     * @param int $mode
     *
     * @return $this
     */
    public function setMode($mode)
    {
        if (!$this->isValidMode($mode)) {
            throw new InvalidArgumentException('Unexpected mode provided.');
        }

        $this->mode = $mode;

        return $this;
    }

    /**
     * Get the mode.
     *
     * @return int
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Tell if the provided mode is valid.
     *
     * @param mixed $mode
     *
     * @return bool
     */
    public function isValidMode($mode)
    {
        return is_int($mode) && in_array($mode, [static::MODE_SOFT, static::MODE_MEDIUM, static::MODE_HARD]);
    }

    /**
     * Get or set the mode. If the parameter is not provided or it is null, the current mode is returned. If the
     * parameter is provided and not null, the current mode will be set and the current driver instance is returned.
     *
     * @param null|int $mode
     *
     * @return int|$this
     */
    public function mode($mode = null)
    {
        if (is_null($mode)) {
            return $this->getMode();
        }

        return $this->setMode($mode);
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
}
