<?php

namespace Neat\Object\Test\Helper;

use DateTime;
use Neat\Object\Test\Helper\Event\Custom;

class User extends Entity
{
    use \Neat\Object\Events;

    public const EVENTS = [
        'custom' => Custom::class,
    ];

    /** @var int */
    public $id;

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

    /** @noinspection PhpFullyQualifiedNameUsageInspection */
    /**
     * @var \DateTimeImmutable
     */
    public $registerDate;

    /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
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

    public function properties()
    {
        return $this->hasMany(Property::class);
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
