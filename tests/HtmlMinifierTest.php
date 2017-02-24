<?php

class HtmlMinifierTest extends TestCase
{
    public function testMiniphyHtmlReturnsInstanceOfHtmlMinifier()
    {
        $miniphy = $this->createMiniphyInstance();

        $this->assertInstanceOf('Miniphy\\Drivers\\Html\\RegexDriver', $miniphy->html());
    }

    public function testMinification()
    {
        $htmlMinifier = $this->createMiniphyInstance()->html();

        foreach ($this->directoriesIn('html') as $directory) {
            if (($input = $this->loadFile('html/' . $directory . '/input.html')) !== false) {
                foreach (['soft' => 1, 'medium' => 2, 'hard' => 3] as $modeName => $mode) {
                    if (($output = $this->loadFile('html/' . $directory . '/output_mode_' . $modeName . '.html')) !== false) {
                        $this->assertEquals($output, $htmlMinifier->mode($mode)->minify($input));
                    }
                }
            }
        }
    }
}
