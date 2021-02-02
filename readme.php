<?php

// This file helps with inspection errors in readme.md.

/** @noinspection PhpUnused */

$pdo        = new PDO('mysql:host=localhost;charset=utf8mb4;dbname=test', 'username', 'password');
$connection = new Neat\Database\Connection($pdo);
$policy     = new Neat\Object\Policy();
$manager    = new Neat\Object\Manager($connection, $policy);
$repository = $manager->repository(User::class);
$user       = new User();

class User
{
    use Neat\Object\Storage;
    use Neat\Object\Relations;

    /** @var int */
    public $id;

    /** @var string */
    public $name;

    /** @var int */
    public $alternativeId;

    public function greet()
    {
        echo 'Hello, ' . $this->name . '!';
    }

    public function address(): Neat\Object\Relations\One
    {
        return $this->hasOne(Address::class);
    }

    public function roles(): Neat\Object\Relations\Many
    {
        return $this->belongsToMany(Role::class);
    }
}

class Address
{
}

class Article
{
    /** @var string */
    public $type;
}

class Role
{
    use Neat\Object\Storage;

    /** @var string */
    public $name;

    /** @var bool */
    public $invisible;
}

class AgendaLine
{
    use Neat\Object\Storage;

    /** @var int */
    public $id;

    /** @var int */
    public $appointmentId;

    /** @var string */
    public $description;
}

class Appointment
{
    use Neat\Object\Storage;
    use Neat\Object\Relations;

    /** @var int */
    public $id;

    /** @var int */
    public $createdBy;

    public function creator(): Neat\Object\Relations\One
    {
        return $this->belongsToOne(User::class, 'creator', function (Neat\Object\Relations\Reference\LocalKeyBuilder $builder) {
            // ...
        });
    }

    public function agendaLines(): Neat\Object\Relations\Many
    {
        return $this->hasMany(AgendaLine::class, 'agenda', function (Neat\Object\Relations\Reference\RemoteKeyBuilder $builder) {
            // ...
        });
    }

    public function attendees(): Neat\Object\Relations\Many
    {
        return $this->belongsToMany(User::class, 'attendees', function (Neat\Object\Relations\Reference\JunctionTableBuilder $builder) {
            // ...
        });
    }
}
