<?php

namespace Neat\Object\Test\Helper;

use Neat\Object\ArrayConversion;
use Neat\Object\Entity;
use Neat\Object\Test\Helper\GroupRepository;

class Group extends Entity
{
    use ArrayConversion;

    const REPOSITORY = GroupRepository::class;

    /**
     * @var integer
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $description;
}
