<?php

namespace Neat\Object\Test\Helper;

use DateTime;
use Neat\Object\Entity;

class User extends Entity
{
    /**
     * Static no storage field
     *
     * @var null
     */
    public static $nostorage;

    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
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
}
