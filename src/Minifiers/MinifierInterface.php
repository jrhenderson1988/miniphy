<?php

namespace Miniphy\Minifiers;

interface MinifierInterface
{
    /**
     * Minify the provided content.
     *
     * @param string $content
     *
     * @return string
     */
    public function minify($content);
}
