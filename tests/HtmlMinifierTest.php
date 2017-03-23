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

    public function testHtmlMinificationInPHPTemplates()
    {
        $miniphy = $this->createMiniphyInstance();

        foreach ($this->directoriesIn('php') as $directory) {
            if (($input = $this->loadFile('php/' . $directory . '/input.php')) !== false) {
                $modes = [
                    'soft' => Miniphy::HTML_MODE_SOFT,
                    'medium' => Miniphy::HTML_MODE_MEDIUM,
                    'hard' => Miniphy::HTML_MODE_HARD
                ];

                foreach ($modes as $modeName => $mode) {
                    $miniphy->setHtmlMode($mode);
                    if (($output = $this->loadFile('php/' . $directory . '/output_mode_' . $modeName . '.php')) !== false) {
                        if ($directory == 3) {
                            $minified = $miniphy->html($input);

                            for ($i = 0, $len = mb_strlen($output); $i < $len; $i++) {
                                if (mb_substr($minified, $i, 1) == mb_substr($output, $i, 1)) {

                                } else {
                                    echo "\n";
                                    var_dump(mb_substr($minified, $i), mb_substr($output, $i));
                                    break;
                                }
                            }
                            die();
                        }

                        $this->assertEquals($output, $miniphy->html($input));
                    }
                }
            }
        }
    }
}
