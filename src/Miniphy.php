<?php

namespace Miniphy;

use InvalidArgumentException;
use Miniphy\Drivers\HtmlDriver;
use Miniphy\Helpers\StringHelper;

class Miniphy
{
    const HTML_MODE_SOFT = 1;
    const HTML_MODE_MEDIUM = 2;
    const HTML_MODE_HARD = 3;

    /**
     * The HTML mode.
     *
     * @var int
     */
    protected $htmlMode = 1;

    /**
     * String utilities class.
     *
     * @var \Miniphy\Helpers\StringHelper
     */
    protected $stringHelper;

    /**
     * Holds an instance of the HTML driver.
     *
     * @var \Miniphy\Drivers\HtmlDriver
     */
    protected $htmlDriver;

    /**
     * Miniphy constructor.
     */
    public function __construct()
    {
        $this->stringHelper = new StringHelper();
    }

    /**
     * Get (Or create it if it does not already exist) the HTML driver. If non-null content is provided, the HTML driver
     * will be used to minify the provided content and that will be returned instead.
     *
     * @param string|null $content
     *
     * @return \Miniphy\Drivers\HtmlDriver|string
     */
    public function html($content = null)
    {
        if (!$this->htmlDriver) {
            $this->htmlDriver = new HtmlDriver($this);
        }

        return is_null($content) ? $this->htmlDriver : $this->htmlDriver->minify($content);
    }

    /**
     * Set the mode. The current instance is returned for chaining.
     *
     * @param int $mode
     *
     * @return $this
     */
    public function setHtmlMode($mode)
    {
        if (!$this->isValidHtmlMode($mode)) {
            throw new InvalidArgumentException('Unexpected mode provided.');
        }

        $this->htmlMode = $mode;

        return $this;
    }

    /**
     * Get the mode.
     *
     * @return int
     */
    public function getHtmlMode()
    {
        return $this->htmlMode;
    }

    /**
     * Tell if the provided mode is valid.
     *
     * @param mixed $mode
     *
     * @return bool
     */
    public function isValidHtmlMode($mode)
    {
        return is_int($mode) && in_array($mode, [
            static::HTML_MODE_SOFT, static::HTML_MODE_MEDIUM, static::HTML_MODE_HARD
        ]);
    }

    /**
     * Get or set the mode. If the parameter is not provided or it is null, the current mode is returned. If the
     * parameter is provided and not null, the current mode will be set and the current driver instance is returned.
     *
     * @param null|int $mode
     *
     * @return int|$this
     */
    public function htmlMode($mode = null)
    {
        if (is_null($mode)) {
            return $this->getHtmlMode();
        }

        return $this->setHtmlMode($mode);
    }

    /**
     * Get the string helper.
     *
     * @return \Miniphy\Helpers\StringHelper
     */
    public function getStringHelper()
    {
        return $this->stringHelper;
    }
}
