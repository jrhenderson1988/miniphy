<?php

namespace Miniphy\Drivers\Html;

use Miniphy\Miniphy;

/**
 * TODO [Reserving PHP tags]
 *      - Take into account short opening tags if turned on
 *      - Take into account the short echo style tags <?= ... ?>
 *      - Take into account the tag endings that are within strings e.g. <?php echo '?>'; ?>
 *      - Take into account tag endings that are within multi-line comments
 *
 * NOTE: Placing a closing PHP tag ?> inside a multi-line comment appears to be allowed. However, they don't seem to be
 *       allowed for single line comments.
 */
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

        $content = $this->reservePhpTags($content);

        $content = $this->reservePres($content);
        $content = $this->reserveTextAreas($content);
        $content = $this->reserveScripts($content);
        $content = $this->reserveStyles($content);
        $content = $this->removeHtmlComments($content);
        $content = $this->trimLines($content);
        $content = $this->removeNewLineCharactersBetweenAttributes($content);
        $content = $this->removeWhiteSpace($content);
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
     * Reserve all PHP tags in the content.
     *
     * @param string $content
     *
     * @return string
     */
    protected function reservePhpTags($content)
    {
//        $length = mb_strlen($content);
//        if (($from = mb_strpos($content, '<?php')) !== false) {
//            $inDoubleQuotedString = false;
//            $inSingleQuotedString = false;
//            $inMultilineComment = false;
//
//            for ($to = $from + 5; $to < $length; $i++) {
//                $char = mb_substr($content, $to, 1);
//
//                if ($inDoubleQuotedString) {
//                    if ($char == '\\') {
//                        $to += 1;
//                    } elseif ($char == '"') {
//                        $inDoubleQuotedString = false;
//                    }
//                } elseif ($inSingleQuotedString) {
//                    if ($char == '\\') {
//                        $to += 1;
//                    } elseif ($char == '\'') {
//                        $inSingleQuotedString = false;
//                    }
//                } elseif ($inMultilineComment) {
//                    if ($char == '*' && mb_substr($content, $to + 1, 1) == '/') {
//                        $inMultilineComment = false;
//                    }
//                } elseif ($char == '?' && mb_substr($content, $to + 1, 1) == '>') {
//                    $to += 2;
//
//                    dd([$from, $i, mb_substr($content, $from, $to - $from)]);
//                } else {
//                    if ($char == '"') {
//                        $inDoubleQuotedString = true;
//                    }
//
//                    if ($char == '\'') {
//                        $inSingleQuotedString = true;
//                    }
//
//                    if ($char == '/' && mb_substr($content, $to + 1, 1) == '*') {
//                        $inMultilineComment = true;
//                        $to += 1;
//                    }
//                }
//            }
//
//
//        }


        return $this->patternReserve('/<\\?php\\s+?[\\s\\S]+?\\?>/', $content, 'php-tag');
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
     * Remove new line characters between HTML attributes.
     *
     * @param string $content
     *
     * @return string
     */
    protected function removeNewLineCharactersBetweenAttributes($content)
    {
        return $this->patternReplace('/<[a-z0-9-]+?\\b[^>]*?\\/?>/i', function ($matches) {
            if (strpos($matches[0], "\n") === false) {
                return $matches[0];
            }

            // ([^\s"'>\/=]+(?:=(?:(?:"[^"]*")|(?:'[^']*')|(?:[^ ]+)))?)\s*\n\s*
            return $this->patternReplace(
                '/([^\s"\'>\/=]+(?:=(?:(?:"[^"]*")|(?:\'[^\']*\')|(?:[^ ]+)))?)\s*\n\s*/', '$1 ', $matches[0]
            );
        }, $content);
    }

    /**
     * Remove white space before and after HTML opening tags, HTML closing tags and IE conditional comments. The set
     * mode is taken into account and the relevant replacement is made.
     *
     * @param string $content
     *
     * @return string
     */
    protected function removeWhiteSpace($content)
    {
        // Define a callback that will return a single space BEFORE the entire matched tag if it's inline.
        $spaceBeforeIfInline = function ($matches) {
            return ($this->isInline($matches[2]) ? ' ' : '') . $matches[1];
        };

        // Define a callback that will return a single space AFTER the entire matched tag if it's inline.
        $spaceAfterIfInline = function ($matches) {
            return $matches[1] . ($this->isInline($matches[2]) ? ' ' : '');
        };

        // Define a set of mappings from pattern to mode specific replacements.
        $patternMappings = [
            [
                'pattern' => '(<!(?:--\\s*?\\[[^\\]]+?\\]|\\[[^\\]]+?\\]\\s*?--)>)',
                'replacements' => [
                    Miniphy::HTML_MODE_SOFT => [' $1', '$1 '],
                    Miniphy::HTML_MODE_MEDIUM => ['$1', '$1'],
                    Miniphy::HTML_MODE_HARD => ['$1', '$1']
                ]
            ],
            [
                'pattern' => '(<([a-z0-9-]+?)\\b[^>]*?\\/?\\s*?>)',
                'replacements' => [
                    Miniphy::HTML_MODE_SOFT => [' $1', '$1 '],
                    Miniphy::HTML_MODE_MEDIUM => [$spaceBeforeIfInline, $spaceAfterIfInline],
                    Miniphy::HTML_MODE_HARD => ['$1', '$1']
                ]
            ],
            [
                'pattern' => '(<\\/([a-z0-9-]+?)\\b[^>]*>)',
                'replacements' => [
                    Miniphy::HTML_MODE_SOFT => [' $1', '$1 '],
                    Miniphy::HTML_MODE_MEDIUM => [$spaceBeforeIfInline, $spaceAfterIfInline],
                    Miniphy::HTML_MODE_HARD => ['$1', '$1']
                ]
            ]
        ];

        // Go through each pattern mapping and run each pattern with an addition of leading and trailing whitespace. If
        // matches are found, the corresponding before/after replacements are used for the mode we're currently using.
        foreach ($patternMappings as $patternMapping) {
            $pattern = $patternMapping['pattern'];
            $replacements = $patternMapping['replacements'][$this->getMode()];
            $beforeReplacement = $replacements[0];
            $afterReplacement = $replacements[1];

            $content = $this->patternReplace('/\\s+' . $pattern . '/i', $beforeReplacement, $content);
            $content = $this->patternReplace('/' . $pattern . '\\s+/i', $afterReplacement, $content);
        }

        // Return the content.
        return $content;
    }
}
