<?php

namespace Neat\Object\Test;

use Neat\Database\Connection;
use Neat\Object\Model;
use PHPUnit\Framework\TestCase;
use Neat\Object\Tests\User;

class UserTest extends TestCase
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var Connection
     */
    private $connection;

    public function setUp()
    {
        $this->user = new User;
        $pdo = new \PDO('sqlite::memory:');
        $this->connection = new Connection($pdo);
        Model::setConnection($this->connection);
    }

    /**
     * Minify SQL query by removing unused whitespace
     *
     * @param string $query
     * @return string
     */
    protected function minifySQL($query)
    {
        $replace = [
            '|\s+|m'     => ' ',
            '|\s*,\s*|m' => ',',
            '|\s*=\s*|m' => '=',
        ];

        return preg_replace(array_keys($replace), $replace, $query);
    }

    /**
     * Assert SQL matches expectation
     *
     * Normalizes whitespace to make the tests less fragile
     *
     * @param string $expected
     * @param string $actual
     */
    protected function assertSQL($expected, $actual)
    {
        $this->assertEquals(
            $this->minifySQL($expected),
            $this->minifySQL($actual)
        );
    }

    public function testTableName()
    {
        $this->assertEquals('user', User::getTableName());
    }

    public function testQuery()
    {
        $this->assertSQL('SELECT * FROM user', User::query()->getSelectQuery());
    }
}
