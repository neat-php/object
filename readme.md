# Neat Object components

[![Stable Version](https://poser.pugx.org/neat/object/version)](https://packagist.org/packages/neat/object)
[![Build Status](https://travis-ci.org/neat-php/object.svg?branch=master)](https://travis-ci.org/neat-php/object)

Neat object components adds a simple yet intuitive ORM layer on top of the Neat database components.

## Getting started

To install this package, simply issue [composer](https://getcomposer.org) on the
command line:
```
composer require neat/object
```


Then initialize the object manager using the static create factory:
```php
<?php

// Connecting is easy, just pass a PDO instance
$pdo        = new PDO('mysql:host=localhost;dbname=test', 'username', 'password');
$connection = new Neat\Database\Connection($pdo);
$manager    = Neat\Object\Manager::create($connection);
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
$respository = $manager->repository(User::class);

$user = new User;
$user->name = 'John';

$repository->store($user);

echo $user->id; // 1
```

## Find by identifier
If you know the identifier for your entity, you can access it using the
`has` and `get` methods.
```php
// Get the user at once
$user = $repository->get(1); // Returns user with id 1 or null if not found

// Or just check if it exists
if (!$respository->has(1)) {
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
* `select` returns a query builder that allows chaining any of the methods
  above

Each of these methods can be passed a query in several ways:
```php
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
```

## Find using static access
To prevent littering your code with manager and repository instances, you can
use the `Storage` trait to allow for static repository access:
```php
class User
{
    use Storage;
    
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
    // Do something with every user
}
```

## Relations
If you need relations just use the `Relations` trait which supplies factory functions
for hasOne/Many and belongsToOne/Many relations.
```php
class User
{
    use Storage;
    use Relations;

    public function address(): One
    {
        return $this->hasOne(Address::class);
    }
}

// Returns the address object for the user or null
$address = $user->address()->get();
```
Relations are automatically stored when the parent model is stored:
```php
$address = new Address();
$user->address()->set($address);
$user->store();
// Stores the user
// Sets the Address::$userId
// Stores the address
```
Many(hasMany & belongsToMany) relations implement use `Collectible`:
```php
$user->roles()->map(function (Role $role) {
    return $role->name;
});
```
You can extend the relation classes to add commonly needed functionality:
```php
class ManyOrderArticles extends Many {
    public function filterByType(string $type)
    {
        return $this->filter(function (Article $article) use ($type) {
            return $article->type === $type;
        });
    }
}
```
But you will have to instantiate the relation yourself, copy over the content of the factory method for the appropriate relation and change the relation type and cache key.
