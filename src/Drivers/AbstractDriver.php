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
     * The format of the string that we replace in the content as a placeholder. This string must contain a %key% tag
     * which we will replace with the unique key element.
     *
     * @var string
     */
    protected $reservationTagFormat = '%key%';

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
            $key = (!empty($prefix) ? $prefix . '-' : '') . $this->miniphy->getStringHelper()->random();
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

    /**
     * Replace areas of the provided $content which match the $pattern provided, with the $replacement provided. If a
     * callback is provided as the replacement, then it is passed to preg_replace_callback and the value returned from
     * the callback will be the replacement.
     *
     * @param string $pattern
     * @param callable|string $replacement
     * @param string $content
     *
     * @return string
     */
    protected function patternReplace($pattern, $replacement, $content)
    {
        $method = 'preg_replace' . (is_callable($replacement) ? '_callback' : '');

        return $method($pattern, $replacement, $content);
    }

    /**
     * Remove some content from the value, using the regular expression pattern provided.
     *
     * @param string $pattern
     * @param string $content
     *
     * @return string
     */
    protected function patternRemove($pattern, $content)
    {
        return $this->patternReplace($pattern, '', $content);
    }

    /**
     * Make reservations using the provided pattern.
     *
     * @param string $pattern
     * @param string $content
     * @param string $prefix
     *
     * @return string
     */
    protected function patternReserve($pattern, $content, $prefix = '')
    {
        return $this->patternReplace($pattern, function ($matches) use($prefix) {
            return $this->buildReservationTag($this->reserve($matches[0], $prefix));
        }, $content);
    }

    /**
     * Reserve a substring segment of the provided content given a starting position, a length, some content and an
     * optional prefix.
     *
     * @param int    $from
     * @param int    $length
     * @param string $content
     * @param string $prefix
     *
     * @return string
     */
    protected function substringReserve($from, $length, $content, $prefix = '')
    {
        $reserved = mb_substr($content, $from, $length);
        $replacement = $this->buildReservationTag($this->reserve($reserved, $prefix));
        $before = mb_substr($content, 0, $from);
        $after = mb_substr($content, $from + $length);

        return $before . $replacement . $after;
    }
}
