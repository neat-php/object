<?php

/** @noinspection PhpMissingDocCommentInspection */

namespace Neat\Object\Test\Helper;

use DateTime;
use Neat\Object\Identifier;

class User extends Entity
{
    use Identifier;

    public int $typeId;

    public string $username;

    public string $firstName;

    public $middleName;

    public $lastName;

    public ?Phone $phone;

    public ?bool $active;

    /**
     * @nostorage
     */
    public ?int $ignored;

    /**
     * @noinspection PhpFullyQualifiedNameUsageInspection
     * @note intentional fully qualified class name
     */
    public ?\DateTimeImmutable $registerDate;

    public ?DateTime $updateDate;

    public ?DateTime $deletedDate;

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
