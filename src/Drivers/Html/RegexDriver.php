<?php

namespace Miniphy\Drivers\Html;

class RegexDriver extends AbstractHtmlDriver implements HtmlDriverInterface
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
        $content = $this->reservePres($content);
        $content = $this->reserveTextAreas($content);
        $content = $this->reserveScripts($content);
        $content = $this->reserveStyles($content);
        $content = $this->removeHtmlComments($content);
        $content = $this->trimLines($content);
        $content = $this->removeWhitespaceAroundIEConditionals($content);
        $content = $this->removeWhitespaceAroundHtmlElements($content);
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
            return $this->buildReservationTag($this->reserve($matches[0], $prefix));
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
        $result = '';

        foreach (explode("\n", $content) as $line) {
            $result .= "\n" . trim($line);
        }

        return trim($result);
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

    /**
     * Remove whitespace around HTML elements in the content. There are 3 modes that define the nature of this method.
     *
     * - MODE_SOFT:   Replace whitespace around ALL elements with a single space or newline.
     * - MODE_MEDIUM: Remove ALL whitespace around all non-inline elements (Bear in mind, CSS can make inline elements
     *                behave like block, or block elements like inline etc.)
     * - MODE_HARD:   Remove ALL whitespace around all elements, which may result in inline elements not being spaced.
     *
     * @param string $content
     *
     * @return string
     */
    protected function removeWhitespaceAroundHtmlElements($content)
    {
        $htmlElementPattern = '(<\\/?([a-z0-9-]+?)\\b[^>]*?\\/?>)';
        if ($this->getMode() == static::MODE_SOFT) {
            $content = $this->patternReplace('/\\s+' . $htmlElementPattern . '/i', ' $1', $content);

            return $this->patternReplace('/' . $htmlElementPattern . '\\s+/i', '$1 ', $content);
        } elseif ($this->getMode() == static::MODE_MEDIUM) {
            return $this->patternReplace('/\\s*' . $htmlElementPattern . '\\s*/i', function ($matches) {
                if (!isset($matches[2])) {
                    return $matches[0];
                }

                return $this->isInline($matches[2]) ? $matches[0] : $matches[1];
            }, $content);
        } else {
            return $this->patternReplace('/\\s*' . $htmlElementPattern . '\\s*/i', '$1', $content);
        }
    }
}
