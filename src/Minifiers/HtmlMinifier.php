<?php

namespace Miniphy\Minifiers;

class HtmlMinifier extends AbstractMinifier implements MinifierInterface
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
        return $content;
    }
}
