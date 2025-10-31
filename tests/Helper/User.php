<?php

namespace Neat\Object\Test\Helper;

use DateTime;
use Neat\Object\Relations;
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
     * @var float
     */
    public $score;

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

    public function address(): Relations\One
    {
        return $this->hasOne(Address::class);
    }

    public function properties(): Relations\Many
    {
        return $this->hasMany(Property::class);
    }

    public function type(): Relations\One
    {
        return $this->belongsToOne(Type::class);
    }

    public function groups(): Relations\Many
    {
        return $this->belongsToMany(Group::class);
    }
}
