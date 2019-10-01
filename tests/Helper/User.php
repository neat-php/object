<?php

/** @noinspection PhpUnused */
/** @noinspection PhpFullyQualifiedNameUsageInspection */
/** @noinspection PhpMissingDocCommentInspection */

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
     * @var \Neat\Object\Test\Helper\Phone
     */
    public $phone;

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
     * @var \DateTimeImmutable
     */
    public $registerDate;

    /**
     * @var \DateTime
     * @note intentional fully qualified class name
     */
    public $updateDate;

    /**
     * @var DateTime|null
     */
    public $deletedDate;

    /**
     * Static no storage field
     *
     * @var null
     */
    public static $nostorage;

    public function address()
    {
        return $this->hasOne(Address::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function type()
    {
        return $this->belongsToOne(Type::class);
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class);
    }
}
