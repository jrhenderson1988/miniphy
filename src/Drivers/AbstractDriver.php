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
    protected $defaultReservationReplacement = '%key%';

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
     * @param string $replacement
     * @return string
     */
    protected function reserve($content, $replacement = null)
    {
        $key = null;

        while (is_null($key) || isset($this->reservations[$key])) { // || mb_strpos($target, $content) !== false
            $key = $this->buildReservationTag(
                $this->miniphy->getStringHelper()->random(), $replacement
            );
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
        while (1) {
            $replacements = 0;

            foreach ($this->reservations as $key => $reserved) {
                if (mb_strpos($content, $key) !== false) {
                    $content = str_replace($key, $reserved, $content);
                    $replacements++;
                }
            }

            if ($replacements <= 0) {
                return $content;
            }
        }

        return $content;
    }

    /**
     * Build a reservation tag using the reservation tag format and the provided key.
     *
     * @param string      $key
     * @param string|null $replacement
     *
     * @return string
     */
    protected function buildReservationTag($key, $replacement = null)
    {
        $replacement = !is_null($replacement) ? $replacement : $this->defaultReservationReplacement;

        return str_replace('%key%', $key, $replacement);
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
     * @param string $replacement
     *
     * @return string
     */
    protected function patternReserve($pattern, $content, $replacement = null)
    {
        return $this->patternReplace($pattern, function ($matches) use($replacement) {
            return $this->buildReservationTag($this->reserve($matches[0], $replacement));
        }, $content);
    }

    /**
     * Reserve a substring segment of the provided content given a starting position, a length, some content and an
     * optional prefix.
     *
     * @param int    $from
     * @param int    $length
     * @param string $content
     * @param string $replacement
     *
     * @return string
     */
    protected function substringReserve($from, $length, $content, $replacement = null)
    {
        $reserved = mb_substr($content, $from, $length);
        $replacement = $this->buildReservationTag($this->reserve($reserved, $replacement));
        $before = mb_substr($content, 0, $from);
        $after = mb_substr($content, $from + $length);

        return $before . $replacement . $after;
    }
}
