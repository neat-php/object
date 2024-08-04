<?php /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */

namespace Neat\Object\Test\Helper;

use DateTime;
use Neat\Object\Relations;
use Neat\Object\Test\Helper\Event\Custom;

class TypedUser extends Entity
{
    use \Neat\Object\Events;

    public const EVENTS = [
        'custom' => Custom::class,
    ];

    public int $id;

    public int $typeId;

    public string $username;

    public string $firstName;

    public $middleName;

    public $lastName;

    public bool $active;

    /**
     * @nostorage
     */
    public int $ignored;

    /** @noinspection PhpFullyQualifiedNameUsageInspection */
    public \DateTimeImmutable $registerDate;

    /**
     * @note intentional fully qualified class name
     */
    public \DateTime $updateDate;

    public ?DateTime $deletedDate;

    public self $user;

    public static object $nostorage;

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
