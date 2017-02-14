<?php

use JonathonHenderson\Miniphy\Base;

class HtmlMinifierTest extends TestCase
{
    public function testSomething()
    {
        $value = 'test';

        $base = new Base('test');

        $this->assertEquals($base->get(), $value);
    }
}
