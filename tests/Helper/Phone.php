<?php

/** @noinspection PhpMissingDocCommentInspection */

namespace Neat\Object\Test\Helper;

use Serializable;

class Phone implements Serializable
{
    /** @var string */
    protected $number;

    /**
     * @param string $number
     */
    public function __construct(string $number)
    {
        $this->number = $number;
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return $this->number;
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $this->number = $serialized;
    }
}
