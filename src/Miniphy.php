<?php

namespace Miniphy;

use InvalidArgumentException;
use Miniphy\Drivers\DriverInterface;
use Miniphy\Drivers\Html\RegexDriver as HtmlRegexDriver;
use Miniphy\Exceptions\NoSuchDriverException;
use Miniphy\Helpers\StringHelper;

class Miniphy
{
    const HTML_MODE_SOFT = 1;
    const HTML_MODE_MEDIUM = 2;
    const HTML_MODE_HARD = 3;

    /**
     * Stores instances of all of the created drivers to save re-creating them whenever they're called.
     *
     * @var array
     */
    protected $driverCache = [];

    /**
     * The default HTML driver.
     *
     * @var string
     */
    protected $defaultHtmlDriverKey = 'regex';

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
     * Miniphy constructor.
     */
    public function __construct()
    {
        $this->stringHelper = new StringHelper();
    }

    /**
     * Get a HTML driver.
     *
     * @param string|null $driver
     *
     * @return \Miniphy\Drivers\DriverInterface
     * @throws \Miniphy\Exceptions\NoSuchDriverException
     */
    public function html($driver = null)
    {
        $driver = !is_null($driver) ? $driver : $this->getDefaultHtmlDriverKey();

        return $this->getDriver('html-' . $driver);
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
        return is_int($mode) && in_array($mode, [static::HTML_MODE_SOFT, static::HTML_MODE_MEDIUM, static::HTML_MODE_HARD]);
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
     * Get the driver specified by the base and key from the driver cache. Create the driver if it does not exist.
     *
     * @param string $key
     *
     * @return DriverInterface
     * @throws \Miniphy\Exceptions\NoSuchDriverException
     */
    protected function getDriver($key)
    {
        if (isset($this->driverCache[$key])) {
            return $this->driverCache[$key];
        }

        $method = 'create' . $this->getStringHelper()->studly($key) . 'Driver';
        if (!method_exists($this, $method)) {
            throw new NoSuchDriverException("The specified driver, '{$key}' does not exist.");
        }

        return $this->driverCache[$key] = $this->$method();
    }

    /**
     * Create a Regex HTML driver.
     *
     * @return \Miniphy\Drivers\Html\RegexDriver
     */
    public function createHtmlRegexDriver()
    {
        return new HtmlRegexDriver($this);
    }

    /**
     * Get the default HTML driver key.
     *
     * @return string
     */
    public function getDefaultHtmlDriverKey()
    {
        return $this->defaultHtmlDriverKey;
    }

    /**
     * Set the default HTML driver key.
     *
     * @param string $key
     */
    public function setDefaultHtmlDriverKey($key)
    {
        $this->defaultHtmlDriverKey = $key;
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
