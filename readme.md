# Neat Object components

[![Stable Version](https://poser.pugx.org/neat/object/version)](https://packagist.org/packages/neat/object)
[![Build Status](https://travis-ci.org/neat-php/object.svg?branch=master)](https://travis-ci.org/neat-php/object)
[![codecov](https://codecov.io/gh/neat-php/object/branch/master/graph/badge.svg)](https://codecov.io/gh/neat-php/object)

Neat object component adds a simple yet intuitive ORM layer on top of the Neat database component.

## Getting started

To install this package, simply issue [composer](https://getcomposer.org) on the
command line:
```
composer require neat/object
```


Then initialize the object manager:
```php
<?php

// Initialize the manager using a database connection and an object policy
$pdo        = new PDO('mysql:host=localhost;charset=utf8mb4;dbname=test', 'username', 'password');
$connection = new Neat\Database\Connection($pdo);
$policy     = new Neat\Object\Policy();
$manager    = new Neat\Object\Manager($connection, $policy);

// If you want easy access to static methods, set the Manager instance
Neat\Object\Manager::set($manager);

// Or set a factory that connects to the database only when needed
Neat\Object\Manager::setFactory(function () {
    $pdo        = new PDO('dsn', 'username', 'password');
    $connection = new Neat\Database\Connection($pdo);
    $policy     = new Neat\Object\Policy();

    return new Neat\Object\Manager($connection, $policy);
});
```

## Creating an entity
Entities can be just plain old PHP objects
```php
class User
{
    /** @var int */
    public $id;

    /** @var string */
    public $name;
}
```

To persist these entities into the database, we can use a repository:
```php
$repository = Neat\Object\Manager::get()->repository(User::class);

$user = new User();
$user->name = 'John';

$repository->store($user);

echo $user->id; // 1
```

## Find by identifier
If you know the identifier for your entity, you can access it using the
`has` and `get` methods.
```php
$repository = Neat\Object\Manager::get()->repository(User::class);

// Get the user at once
$user = $repository->get(1); // Returns user with id 1 or null if not found

// Or just check if it exists
if (!$repository->has(1)) {
    throw new Exception('boohoo');
}
```
To find and entity from a table using a composed primary key, you should pass
the identifiers as an array.

## Find using a query
The repository allows you to query for entities in many ways:
* `one` returns one entity (or null if none matched the query)
* `all` returns all entities matched by the query as an array
* `collection` returns a collection instance containing the matched
  entities
* `iterate` returns a generator allowing you to iterate over the matched
  entities
* `select` returns a mutable query builder that allows chaining any of the
  methods above
* `sql` returns a query object using a handwritten SQL query supplied as string

Each of these methods can be passed a query in several ways:
```php
$repository = Neat\Object\Manager::get()->repository(User::class);

// Find one user with name John (note the [key => value] query array)
$user = $repository->one(['name' => 'John']);

// Find all users that have been deleted (the query is an SQL where clause)
$user = $repository->all('deleted = 1');

// Find all users using a complex query
$administrators = $repository
    ->select('u')
    ->innerJoin('user_group', 'ug', 'u.id = ug.user_id')
    ->innerJoin('group', 'g', 'g.id = ug.group_id')
    ->where('g.name = ?', 'administrators')
    ->orderBy('u.name')
    ->all();

// Get one user using your own SQL query
$user = $repository->sql('SELECT * FROM users WHERE id = ?', 1)->one();

// Or multiple in an array
$active = $repository->sql('SELECT * FROM users WHERE deleted = 0')->all();
```

## Find using static access
To prevent littering your code with manager and repository instances, you can
use the `Storage` trait to allow for static repository access:
```php
class User
{
    use Neat\Object\Storage;

    /** @var int */
    public $id;

    /** @var string */
    public $name;
}

// The Storage trait gives you static access to repository methods
$user = User::get(1);
$users = User::all();
$latest = User::select()->orderBy('created_at DESC')->one();
foreach (User::iterate() as $user) {
    $user->greet();
}
```

## Relations
If you need relations just use the `Relations` trait which supplies factory functions
for hasOne/-Many and belongsToOne/-Many relations.
```php
class User
{
    use Neat\Object\Storage;
    use Neat\Object\Relations;

    public function address(): Neat\Object\Relations\One
    {
        return $this->hasOne(Address::class);
    }
}

$user = User::one(...);

// Returns the address object for the user or null
$address = $user->address()->get();

// Relations are automatically stored when the parent model is stored:
$address = new Address();
$user->address()->set($address);
$user->store();
// Stores the user
// Sets the Address::$userId
// Stores the address
```
When you have multiple relations to the same class, make sure you assign
each of them a unique role using the second parameter to avoid collisions
between them:
```php
class Appointment
{
    use Neat\Object\Storage;
    use Neat\Object\Relations;

    public function createdBy(): Neat\Object\Relations\One
    {
        return $this->belongsToOne(User::class, 'creator');
    }

    public function updatedBy(): Neat\Object\Relations\One
    {
        return $this->belongsToOne(User::class, 'updater');
    }
}
```

## References
The column names and table names used for each relation have defaults that
are determined by the `Policy`. When these defaults don't work, you can
override them by passing a configuration closure as third parameter to the
relation method of you choice:
```php
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

class User
{
    use Neat\Object\Storage;

    /** @var int */
    public $id;

    /** @var int */
    public $alternativeId;
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
```

## Accessors
Accessor methods allow you to call methods like ```add```, ```all```, ```has```,
```get```, ```remove```, ```select``` and ```set``` on your entity object
directly:
```php
class UserAccount
{
    use Neat\Object\Storage;
    use Neat\Object\Relations;

    // Use the Accessors trait to add accessor methods
    use Neat\Object\Accessors;

    public function address(): Neat\Object\Relations\One
    {
        return $this->belongsToOne(Address::class);
    }

    public function roles(): Neat\Object\Relations\Many
    {
        return $this->belongsToMany(Role::class);
    }
}

$user = UserAccount::one(...);

$user->getAddress(); // same as $user->address()->get();
$user->setAddress(...); // same as $user->address()->set(...);

$user->addRole(...); // same as $user->roles()->add(...);
$user->hasRole(...); // same as $user->roles()->has(...);
$user->deleteRole(...); // same as $user->roles()->delete(...);
$user->getRoles(); // same as $user->roles()->get();
$user->selectRoles(); // same as $user->roles()->select();
```

Translating ```$user->addRole()``` to ```$user->roles()->add()``` is done by
the Policy. In its constructor you can provide a pluralize function to allow
for proper translations:
```php
// Use the Policy with custom $pluralize function to initialize your Manager
$policy = new Neat\Object\Policy(null, function (string $singular): string {
    return $singular . 's'; // lousy way of pluralizing relation names
});
```

## Collections
Collections wrap an array of multiple items and offer a chainable way of
accessing these items using several operations. Relations to multiple
instances of a class (hasMany and belongsToMany) offer the same
`Collectible` API:
```php
class User
{
    use Neat\Object\Storage;
    use Neat\Object\Relations;

    public function roles(): Neat\Object\Relations\Many
    {
        return $this->belongsToMany(Role::class);
    }
}

$user = User::one(...);

// Both of these offer the Collectible API
$roles = Role::collection();
$roles = $user->roles();

// Get all roles, the first or the last role
$all = $user->roles()->all();
$first = $user->roles()->first();
$last = $user->roles()->last();

// Count roles
$count = $user->roles()->count();

// Get a filtered collection of roles
$filtered = $user->roles()->filter(function (Role $role) {
    return !$role->invisible;
});

// Get a sorted collection of roles
$sorted = $user->roles()->sort(function (Role $a, Role $b) {
    return $a->name <=> $b->name;
});

// Map roles and get the results in a collection
$names = $user->roles()->map(function (Role $role) {
    return $role->name;
});

// Or get the values of a single property in a collection
$names = $user->roles()->column('name');

// Chain multiple collection functions, then get an array of roles
$result = $user->roles()->filter(...)->sort(...)->all();
```
