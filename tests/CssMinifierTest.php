<?php

class CssMinifierTest extends TestCase
{
    public function testMiniphyCssReturnsInstanceOfCssMinifier()
    {
        $miniphy = $this->createMiniphyInstance();

        $this->assertInstanceOf('Miniphy\\Drivers\\Css\\RegexDriver', $miniphy->css());
    }

    public function testMinification()
    {
        $cssMinifier = $this->createMiniphyInstance()->css();

        foreach ($this->directoriesIn('css') as $directory) {
            if (($input = $this->loadFile('css/' . $directory . '/input.css')) !== false) {
                if (($output = $this->loadFile('css/' . $directory . '/output.css')) !== false) {
                    $this->assertEquals($output, $cssMinifier->minify($input));
                }
            }
        }
    }
}
