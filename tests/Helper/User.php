<?php

namespace Neat\Object\Test\Helper;

use DateTime;
use Neat\Object\Identifier;

class User extends Entity
{
    use Identifier;

    /** @var int */
    public $typeId;

    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $firstName;

    /**
     * @var
     */
    public $middleName;

    public $lastName;

    /**
     * @var bool
     */
    public $active;

    /**
     * @var int
     * @nostorage
     */
    public $ignored;

    /**
     * @var \DateTime
     * @note intentional fully qualified class name
     */
    public $updateDate;

    /**
     * @var DateTime
     */
    public $deletedDate;

    /**
     * Static no storage field
     *
     * @var null
     */
    public static $nostorage;
}
