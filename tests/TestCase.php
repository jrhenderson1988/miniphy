<?php

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    public function createMiniphyInstance()
    {
        return new Miniphy\Miniphy();
    }
}
