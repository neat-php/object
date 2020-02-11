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

    public function greet()
    {
        echo 'Hello, ' . $this->name . '!';
    }

    public function address(): Neat\Object\Relation\One
    {
        return $this->hasOne(Address::class);
    }

    public function roles(): Neat\Object\Relation\Many
    {
        return $this->hasMany(Role::class);
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
    /** @var string */
    public $name;
}
