<?php

namespace Miniphy;

use Miniphy\Minifiers\HtmlMinifier;

class Miniphy
{
    /**
     * @var HtmlMinifier
     */
    protected $htmlMinifier;

    /**
     * Get an instance of the HTML minifier if the parameter is omitted or is null. Run some content through the
     * minifier and return the result if the content is non-null.
     *
     * @param string|null $content
     *
     * @return \Miniphy\Minifiers\HtmlMinifier|string
     */
    public function html($content = null)
    {
        $minifier = $this->getHtmlMinifier();

        return is_null($content) ? $minifier : $minifier->minify($content);
    }

    /**
     * Get an instance of the HTML minifier.
     *
     * @return \Miniphy\Minifiers\HtmlMinifier
     */
    protected function getHtmlMinifier()
    {
        if (!$this->htmlMinifier) {
            $this->htmlMinifier = new HtmlMinifier($this);
        }

        return $this->htmlMinifier;
    }
}
