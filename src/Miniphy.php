<?php

namespace Miniphy;

use Closure;
use Miniphy\Drivers\DriverInterface;
use Miniphy\Drivers\Html\RegexDriver as HtmlRegexDriver;
use Miniphy\Exceptions\NoSuchDriverException;
use Miniphy\Helpers\StringHelper;

class Miniphy
{
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
