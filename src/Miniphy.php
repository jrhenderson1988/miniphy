<?php

namespace Miniphy;

use Closure;
use Miniphy\Drivers\Html\RegexDriver as HtmlRegexDriver;
use Miniphy\Exceptions\NoSuchDriverException;

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

        $method = 'getHtml' . trim(ucfirst(strtolower($driver))) . 'Driver';

        if (!method_exists($this, $method)) {
            throw new NoSuchDriverException("The specified driver, '{$driver}' does not exist.");
        }

        return $this->$method();
    }

    /**
     * Create a Regex HTML driver.
     *
     * @return \Miniphy\Drivers\Html\RegexDriver
     */
    public function getHtmlRegexDriver()
    {
        return $this->getDriver('html.regex', function () {
            return new HtmlRegexDriver($this);
        });
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
     * Create a driver with the provided key and closure.
     *
     * @param string $key
     * @param \Closure $closure
     *
     * @return mixed
     */
    protected function getDriver($key, Closure $closure)
    {
        if (!isset($this->driverCache[$key])) {
            $this->driverCache[$key] = $closure();
        }

        return $this->driverCache[$key];
    }
}
