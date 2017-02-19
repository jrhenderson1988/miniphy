<?php

namespace Miniphy\Drivers;

use Miniphy\Miniphy;

abstract class AbstractDriver
{
    /**
     * @var \Miniphy\Miniphy
     */
    protected $miniphy;

    /**
     * A simple key/value store to hold reserved content.
     *
     * @var array
     */
    protected $reservations = [];

    /**
     * The format of the string that we replace in the content as a placeholder. Generally, minification will work best
     * if the format of the reservation tag represents an already minified HTML element that would be accounted for
     * during the minification process, such as a div tag with an ID.
     *
     * @var string
     */
    protected $reservationTagFormat = '<div id="%key%"></div>';

    /**
     * AbstractDriver constructor.
     *
     * @param \Miniphy\Miniphy $miniphy
     */
    public function __construct(Miniphy $miniphy)
    {
        $this->miniphy = $miniphy;
    }

    /**
     * Reserve the the provided content and return the randomly generated, unique key.
     *
     * @param string $content
     * @param string $prefix
     * @return string
     */
    protected function reserve($content, $prefix = '')
    {
        $key = (!empty($prefix) ? $prefix . '-' : '') . $this->miniphy->getStringHelper()->random();

        while (isset($this->reservations[$key])) {
            $key = (!empty($prefix) ? $prefix . '-' : '') . $this->string->getStringHelper()->random();
        }

        $this->reservations[$key] = $content;

        return $key;
    }

    /**
     * Restore the previously reserved items back into the minified content.
     *
     * @param string $content
     *
     * @return string
     */
    protected function restoreReservations($content)
    {
        foreach ($this->reservations as $key => $reserved) {
            $content = str_replace($this->buildReservationTag($key), $reserved, $content);
        }

        return $content;
    }

    /**
     * Build a reservation tag using the reservation tag format and the provided key.
     *
     * @param string $key
     *
     * @return string
     */
    protected function buildReservationTag($key)
    {
        return str_replace('%key%', $key, $this->reservationTagFormat);
    }
}
