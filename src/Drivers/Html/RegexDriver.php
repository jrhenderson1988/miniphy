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
        return $content . 'regex';
    }
}
