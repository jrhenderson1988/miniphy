<?php

namespace JRHenderson\Miniphy;

class Base
{
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function get()
    {
        return $this->value;
    }
}
