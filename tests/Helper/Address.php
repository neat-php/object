<?php

namespace Neat\Object\Test\Helper;

use Neat\Object\Relations\BelongsToOne;

class Address
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var int
     */
    public $userId;

    /**
     * @var string
     */
    public $street;

    /**
     * @var int
     */
    public $houseNumber;

    /**
     * @var string
     */
    public $zipCode;

    /**
     * @var string
     */
    public $city;

    /**
     * @var string
     */
    public $country;

    /**
     * @return BelongsToOne
     */
    public function user()
    {
        return $this->belongsToOne(User::class);
    }

    public function getUser()
    {
        return $this->user()->get();
    }

    public function setUser(User $user)
    {
        $this->user()->set($user);

        return $this;
    }
}
