<?php

namespace Neat\Object\Test\Helper;

use Neat\Object\Entity;

class Group extends Entity
{
    const REPOSITORY = GroupRepository::class;

    /**
     * @var integer
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $description;

    public static function getRemoteIdentifier()
    {
        return 'groupid';
    }
}
