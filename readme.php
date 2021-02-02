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
        // Pass reference configuration to belongsToOne as
        // callable(LocalKeyBuilder)
        return $this->belongsToOne(User::class, 'creator', function (Neat\Object\Relations\Reference\LocalKeyBuilder $builder) {
            // Use the local property name
            $builder->setLocalKey('createdBy');

            // Or alternatively, the local column name
            $builder->setLocalKeyColumn('created_by');

            // Set the remote property name
            $builder->setRemoteKey('alternativeId');

            // Or alternatively, the remote column name
            $builder->setRemoteKeyColumn('alternative_id');
        });
    }

    public function agendaLines(): Neat\Object\Relations\Many
    {
        // Pass reference configuration to hasOne and hasMany as
        // callable(RemoteKeyBuilder)
        return $this->hasMany(AgendaLine::class, 'agenda', function (Neat\Object\Relations\Reference\RemoteKeyBuilder $builder) {
            // The same local and remote key setters as with belongsToOne
            // can be used with hasMany and hasOne relations.
        });
    }

    public function attendees(): Neat\Object\Relations\Many
    {
        // Pass reference configuration to belongsToMany as
        // callable(JunctionTableBuilder)
        return $this->belongsToMany(User::class, 'attendees', function (Neat\Object\Relations\Reference\JunctionTableBuilder $builder) {
            // Set the junction table name and column names in addition to
            // the same local and remote key setters as with belongsToOne.
            $builder->setJunctionTable('appointment_attendee');
            $builder->setJunctionTableLocalKeyColumn('appointment_id');
            $builder->setJunctionTableRemoteKeyColumn('attendee_id');
            // Please note that the junction table doesn't have an entity
            // class. Therefore you cannot use class and property names.
        });
    }
}
