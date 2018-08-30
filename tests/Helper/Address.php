<?php

namespace Neat\Object\Test\Helper;

class Address
{
    const TABLE = 'my_address_table';

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
}
