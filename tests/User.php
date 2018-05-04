<?php

namespace Neat\Object\Test;

use Neat\Object\Entity;

class User extends Entity
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
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
     * @var string
     */
    public $middleName;

    public $lastName;

    /**
     * @var bool
     */
    public $active;

    /**
     * @var \DateTime
     */
    public $updateDate;

//    public function type()
//    {
//        return $this->belongsToOne(Type::class);
//    }
//
//    public function groups()
//    {
//        return $this->belongsToMany(Group::class);
//    }
}
