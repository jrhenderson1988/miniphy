<?php

use Miniphy\Base;

class HtmlMinifierTest extends TestCase
{
    public function testMiniphyHtmlReturnsInstanceOfHtmlMinifier()
    {
        $miniphy = $this->createMiniphyInstance();

        $this->assertInstanceOf('Miniphy\\Minifiers\\HtmlMinifier', $miniphy->html());
    }

    public function testSomethingElse()
    {
        $value = 'This is a test value';
        $miniphy = $this->createMiniphyInstance();

        $this->assertEquals($value, $miniphy->html($value));
    }
}
