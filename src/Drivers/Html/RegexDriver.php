<?php

namespace Miniphy\Drivers\Html;

use Miniphy\Drivers\AbstractDriver;
use Miniphy\Drivers\DriverInterface;

class RegexDriver extends AbstractDriver implements DriverInterface
{
    /**
     * Minify the provided content.
     *
     * @param string $content
     *
     * @return string
     */
    public function minify($content)
    {
        $content = $this->normalise($content);
        $content = $this->reserveTextAreas($content);
        $content = $this->reservePres($content);
        $content = $this->reserveScripts($content);
        $content = $this->reserveStyles($content);
        $content = $this->removeHtmlComments($content);
        $content = $this->trimLines($content);
        $content = $this->removeWhitespaceAroundIEConditionals($content);
        $content = $this->removeWhitespaceAroundHtmlTags($content);
        $content = $this->replaceExistingLineBreaksWithSpace($content);
        $content = $this->restoreReservations($content);

        return $content;
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
            return str_replace('%key%',  $this->reserve($matches[0], $prefix), $this->reservationTagFormat);
        }, $content);
    }

    /**
     * Reserve all instances of a specified HTML tag and replace them with placeholders.
     *
     * @param string $tag
     * @param string $content
     * @param string $prefix
     *
     * @return string
     */
    protected function reserveTags($tag, $content, $prefix = '')
    {
        $tag = strtolower(trim($tag));

        return $this->patternReserve(
            "/<{$tag}\\b[^>]*?>([\\s\\S]*?)<\\/{$tag}>/i",
            $content,
            !empty($prefix) ? $prefix : $tag
        );
    }

    /**
     * Normalise the content, by converting all \r\n line breaks to \n and trimming off any surrounding whitespace.
     *
     * @param string $content
     * @return string
     */
    protected function normalise($content)
    {
        return str_replace("\r\n", "\n", trim($content));
    }

    /**
     * Reserve all pre elements in the content.
     *
     * @param string $content
     *
     * @return string
     */
    protected function reservePres($content)
    {
        return $this->reserveTags('pre', $content);
    }

    /**
     * Reserve all textarea elements in the content.
     *
     * @param string $content
     *
     * @return string
     */
    protected function reserveTextAreas($content)
    {
        return $this->reserveTags('textarea', $content);
    }

    /**
     * Reserve all script elements in the content.
     *
     * @param string $content
     * @return string
     */
    protected function reserveScripts($content)
    {
        return $this->reserveTags('script', $content);
    }

    /**
     * Reserve all style elements in the content.
     *
     * @param string $content
     * @return string
     */
    protected function reserveStyles($content)
    {
        return $this->reserveTags('style', $content);
    }

    /**
     * Remove HTML comments. IE conditionals will be preserved.
     *
     * @param string $content
     *
     * @return string
     */
    protected function removeHtmlComments($content)
    {
        return $this->patternReplace('/<!--([\s\S]*?)-->/i', function($matches) {
            return strpos($matches[0], '[if') === false || strpos($matches[0], 'endif') === false ? '' : $matches[0];
        }, $content);
    }

    /**
     * Trim each line in the content by removing whitespace at the beginning and end.
     *
     * @param string $content
     *
     * @return string
     */
    protected function trimLines($content)
    {
        return $this->patternRemove('/^\\s+|\\s+$/m', $content);
    }

    /**
     * Remove whitespace around IE conditional comments, both the opening and closing tags.
     *
     * @param string $content
     *
     * @return string
     */
    protected function removeWhitespaceAroundIEConditionals($content)
    {
        $pattern = '/\\s+(<!(?:--\\s*?\\[[^\\]]+?\\]|\\[[^\\]]+?\\]\\s*?--)>)/';

        return $this->patternReplace($pattern, '$1', $content);
    }

    /**
     * Remote whitespace around opening and closing HTML tags. Allow also for self closing tags.
     *
     * @param string $content
     *
     * @return string
     */
    protected function removeWhitespaceAroundHtmlTags($content)
    {
        $pattern = '/\\s*?(<([a-z0-9-]+?\\b[^>]*?\\s*?\\/?|\\/[a-z0-9-]+?\\s*?)>)\s*/i';

        return $this->patternReplace($pattern, ' $1 ', $content);
    }

    /**
     * Replace existing
     *
     * @param string $content
     *
     * @return string
     */
    protected function replaceExistingLineBreaksWithSpace($content)
    {
        $pattern = '/\\n+/';

        return $this->patternReplace($pattern, ' ', $content);
    }
}
