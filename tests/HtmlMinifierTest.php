<?php

class HtmlMinifierTest extends TestCase
{
    public function testMiniphyHtmlReturnsInstanceOfHtmlMinifier()
    {
        $miniphy = $this->createMiniphyInstance();

        $this->assertInstanceOf('Miniphy\\Drivers\\Html\\RegexDriver', $miniphy->html());
    }

    public function testSomethingElse()
    {
        $value = 'test';
        $miniphy = $this->createMiniphyInstance();

        $this->assertEquals($value, $miniphy->html()->minify($value));
    }
}
