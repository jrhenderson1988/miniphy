<?php

namespace Miniphy;

use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;

class MiniphyCompiler extends BladeCompiler
{
    /**
     * @var \Miniphy\Miniphy
     */
    protected $miniphy;

    /**
     * Compiler constructor.
     *
     * @param Miniphy $miniphy
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     * @param $cachePath
     */
    public function __construct(Miniphy $miniphy, Filesystem $filesystem, $cachePath)
    {
        parent::__construct($filesystem, $cachePath);

        $this->miniphy = $miniphy;
    }

    /**
     * Override the default BladeCompiler's compileString method to run it's response through the minifier.
     *
     * @param string $value
     *
     * @return string
     */
    public function compileString($value)
    {
        return $this->miniphy->html()->minify(parent::compileString($value));
    }
}
