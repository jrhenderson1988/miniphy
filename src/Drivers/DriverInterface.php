<?php

namespace Miniphy\Drivers;

interface DriverInterface
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
