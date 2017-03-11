<?php

namespace Miniphy\Drivers\Css;

class RegexDriver extends AbstractCssDriver implements CssDriverInterface
{
    public function minify($content)
    {
        $content = $this->removeComments($content);

        return $content;
    }

    public function removeComments($content)
    {
        return preg_replace('/\/\*[\S\s]*?\*\//', '', $content);
    }

    public function reserveStrings($content)
    {
        return $content;
    }
}
