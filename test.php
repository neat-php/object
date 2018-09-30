<?php

use Neat\Object\Test\Helper\User;

require_once 'vendor/autoload.php';

$factory = new \Neat\Object\Test\Helper\Factory();
$factory->manager();

$user = User::get(1);

dump($user->address()->get());
dump($user->addresses()->get());
dump($user->type()->get());
$type         = new \Neat\Object\Test\Helper\Type;
$type->id     = 1;
$type->name   = 'test';
$user->typeId = 1;
\Neat\Object\Manager::instance()->repository(\Neat\Object\Test\Helper\Type::class)->store($type);
dump($user->type()->load()->get());
