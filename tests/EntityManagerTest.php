<?php

namespace Neat\Object\Test;

use Neat\Object\Test\Helper\Factory;
use PHPUnit\Framework\TestCase;

class EntityManagerTest extends TestCase
{
    /**
     * @var Factory
     */
    private $create;

    public function setUp()
    {
        $this->create = new Factory($this);
    }

    public function testGetConnection()
    {
        $this->assertEquals($this->create->connection(), $this->create->entityManager()->getConnection());
    }
}
