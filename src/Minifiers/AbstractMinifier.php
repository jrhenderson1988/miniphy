<?php

namespace Miniphy\Minifiers;

use Miniphy\Miniphy;

abstract class AbstractMinifier implements MinifierInterface
{
    /**
     * @var \Miniphy\Miniphy
     */
    protected $miniphy;

    /**
     * AbstractMinifier constructor.
     *
     * @param \Miniphy\Miniphy $miniphy
     */
    public function __construct(Miniphy $miniphy)
    {
        $this->miniphy = $miniphy;
    }
}
