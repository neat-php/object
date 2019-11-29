<?php

namespace Neat\Object\Test\Helper;

use Serializable;

class Phone implements Serializable
{
    /**
     * @var int
     */
    private int $phoneNumber;

    public function __construct(int $phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @inheritDoc
     */
    public function serialize()
    {
        return $this->phoneNumber;
    }

    /**
     * @inheritDoc
     */
    public function unserialize($serialized)
    {
        $this->phoneNumber = (int)$serialized;
    }
}
