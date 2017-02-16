<?php

namespace Miniphy\Drivers;

use Miniphy\Miniphy;

abstract class AbstractDriver implements DriverInterface
{
    /**
     * @var \Miniphy\Miniphy
     */
    protected $miniphy;

    /**
     * AbstractDriver constructor.
     *
     * @param \Miniphy\Miniphy $miniphy
     */
    public function __construct(Miniphy $miniphy)
    {
        $this->miniphy = $miniphy;
    }
}
