<?php

use Miniphy\Miniphy;

class HtmlMinifierTest extends TestCase
{
    public function testMiniphyHtmlReturnsInstanceOfHtmlMinifier()
    {
        $miniphy = $this->createMiniphyInstance();

        $this->assertInstanceOf('Miniphy\\Drivers\\HtmlDriver', $miniphy->html());
    }

    public function testHtmlMinification()
    {
        $miniphy = $this->createMiniphyInstance();

        foreach ($this->directoriesIn('html') as $directory) {
            if (($input = $this->loadFile('html/' . $directory . '/input.html')) !== false) {
                $modes = [
                    'soft' => Miniphy::HTML_MODE_SOFT,
                    'medium' => Miniphy::HTML_MODE_MEDIUM,
                    'hard' => Miniphy::HTML_MODE_HARD
                ];

                foreach ($modes as $modeName => $mode) {
                    $miniphy->setHtmlMode($mode);
                    if (($output = $this->loadFile('html/' . $directory . '/output_mode_' . $modeName . '.html')) !== false) {
                        $this->assertEquals($output, $miniphy->html($input));
                    }
                }
            }
        }
    }
}
