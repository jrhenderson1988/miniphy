<?php

namespace Miniphy\Facades;

use Illuminate\Support\Facades\Facade;

class Miniphy extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'miniphy';
    }
}
