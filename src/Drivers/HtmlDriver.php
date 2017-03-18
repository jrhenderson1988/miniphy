<?php

namespace Miniphy\Drivers;

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
class HtmlDriver extends AbstractDriver implements DriverInterface
{
    /**
     * The following elements are generally considered inline in HTML. Sourced from Mozilla documentation:
     * https://developer.mozilla.org/en-US/docs/Web/HTML/Inline_elements
     *
     * @var array
     */
    protected $inlineElements = [
        'a', 'b', 'big', 'i', 'small', 'tt', 'abbr', 'acronym', 'cite', 'code', 'dfn', 'em', 'kbd', 'strong', 'samp',
        'time', 'var', 'bdo', 'br', 'img', 'map', 'object', 'q', 'script', 'span', 'sub', 'sup', 'button', 'input',
        'label', 'select', 'textarea'
    ];

    /**
     * The following elements are generally considered block-level in HTML. Sourced from Mozilla documentation:
     * https://developer.mozilla.org/en-US/docs/Web/HTML/Block-level_elements
     *
     * @var array
     */
    protected $blockElements = [
        'address', 'article', 'aside', 'blockquote', 'br', 'canvas', 'dd', 'div', 'dl', 'fieldset', 'figcaption',
        'figure', 'footer', 'form', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'header', 'hgroup', 'hr', 'li', 'main', 'nav',
        'noscript', 'ol', 'output', 'p', 'pre', 'section', 'table', 'tfoot', 'ul', 'video'
    ];

    /**
     * The tag format that we will use to reserve HTML elements is a div with an ID of the reservation key. This should
     * ensure that the minification process treats reserved items as block elements.
     *
     * @var string
     */
    protected $reservationTagFormat = '<div id="%key%"></div>';

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
     * Get the set of inline elements.
     *
     * @return array
     */
    public function getInlineElements()
    {
        return $this->inlineElements;
    }

    /**
     * Tell if the provided tag is inline by default.
     *
     * @param string $tag
     *
     * @return bool
     */
    protected function isInline($tag)
    {
        return in_array($tag, $this->inlineElements);
    }

    /**
     * Get the set of block elements.
     *
     * @return array
     */
    public function getBlockElements()
    {
        return $this->blockElements;
    }

    /**
     * Tell if the provided tag is block by default.
     *
     * @param string $tag
     *
     * @return bool
     */
    protected function isBlock($tag)
    {
        return in_array($tag, $this->blockElements);
    }

    /**
     * Get the current mode.
     *
     * @return int
     */
    public function getMode()
    {
        return $this->miniphy->getHtmlMode();
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
        $tag = mb_strtolower(trim($tag));

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
        $offset = 0;
        while (($position = mb_strpos($content, '<?php', $offset)) !== false) {
            // To be considered a valid PHP tag, the following character MUST be a whitespace character. If it's not we
            // can simply increase the offset and continue until we find a valid PHP tag.
            if (!in_array(mb_substr($content, $position + 5, 1), [" ", "\t", "\n", "\r", "\0", "\x0B"])) {
                $offset += 5;
                continue;
            }

            // At this point we've found a valid PHP opening tag. We define the following as states when parsing the PHP
            // tag. If we find a closing PHP tag, when in one of these states we can ignore it.
            $inDoubleQuotedString = false;
            $inSingleQuotedString = false;
            $inMultiLineComment = false;

            // Starting at the position after the whitespace character, we're going to loop through the rest of the
            // content.
            for ($i = $position + 6, $length = mb_strlen($content); $i < $length; $i++) {
                // Get the current character.
                $char = mb_substr($content, $i, 1);

                if ($inDoubleQuotedString) {
                    // If we're in a double quoted string and we hit a backslash character, we assume that the following
                    // character is escaped e.g. \r, \n, \\, \", we skip one character by adding 1 to i. If we happen to
                    // hit a double quote character then we've hit an unescaped double quote and therefore are at the
                    // end of the double quoted string.
                    if ($char == '\\') {
                        $i += 1;
                    } elseif ($char == '"') {
                        $inDoubleQuotedString = false;
                    }
                } elseif ($inSingleQuotedString) {
                    // A backslash may be used in a single quoted string to escape a backslash or to escape a single
                    // quote. All other instances of backslash are treated as literal backslashes. Therefore if the
                    // character following the backslash is either another backslash or a single quote, we may simply
                    // skip the following character. If we hit a single quote when in a single quoted string, that is
                    // not preceded by a backslash, then we have hit the end of the string.
                    if ($char == '\\' && in_array(mb_substr($content, $i + 1, 1), ['\'', '\\'])) {
                        $i += 1;
                    } elseif ($char == '\'') {
                        $inSingleQuotedString = false;
                    }
                } elseif ($inMultiLineComment) {
                    // If we're in a multi-line comment and we hit an asterisk (*) character, we can look at the
                    // following character to see if it is a forward slash, which would end the multi-line comment. If
                    // so we can change the state to reflect that we're no longer in a multi-line comment and skip ahead
                    // one character to place the pointer after the forward slash.
                    if ($char == '*' && mb_substr($content, $i + 1, 1) == '/') {
                        $i += 1;
                        $inMultiLineComment = false;
                    }
                } else {
                    // If we're not currently in a double quoted string, single quoted string or a multi-line comment,
                    // we need to check for the relevant characters to check the state. If we hit a double quote
                    // character, the state can be adjusted to indicate that we're in a double quoted string. Similarly,
                    // we can update the state to single quoted string when hitting a single quote character and a
                    // multi-line comment for an asterisk character (*) followed by a forward slash (We can skip ahead
                    // one at this point too). Finally, if we're not in any of the single/double quoted string states or
                    // a multiline comment state and we hit a question mark immediately proceeded by a greater than
                    // symbol, we have hit the end of the PHP tag.
                    if ($char == '"') {
                        $inDoubleQuotedString = true;
                    } elseif ($char == '\'') {
                        $inSingleQuotedString = true;
                    } elseif ($char == '/' && mb_substr($content, $i + 1, 1) == '*') {
                        $inMultiLineComment = true;
                        $i += 1;
                    } elseif ($char == '?' && mb_substr($content, $i + 1, 1) == '>') {
                        $i += 2;
                        $content = $this->substringReserve($position, $i - $position, $content, 'phptag');
                        break;
                    }
                }
            }
        }

        return $content;
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
            return mb_strpos($matches[0], '[if') === false || mb_strpos($matches[0], 'endif') === false ? '' : $matches[0];
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
            if (mb_strpos($matches[0], "\n") === false) {
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
