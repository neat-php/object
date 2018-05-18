<?php

namespace Neat\Object\Test\Helper;

use Neat\Database\Connection;
use Neat\Object\EntityManager;
use Neat\Object\Repository;
use PDO;

class Factory extends \Neat\Database\Test\Factory
{
    /**
     * @var \DateTime
     */
    public $createdDate;

    /**
     * @return PDO
     */
    public function pdo()
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec('CREATE TABLE user (
                      id           INTEGER PRIMARY KEY,
                      type_id      INTEGER  NOT NULL,
                      username     TEXT     NOT NULL,
                      first_name   TEXT     NOT NULL,
                      middle_name  TEXT     NULL,
                      last_name    TEXT     NOT NULL,
                      active       INTEGER  NOT NULL DEFAULT 1,
                      update_date  DATETIME NOT NULL,
                      deleted_date DATETIME NULL
                    )');
        $this->createdDate = new \DateTime();
        $pdo->exec("INSERT INTO `user` (id, type_id, username, first_name, middle_name, last_name, active, update_date, deleted_date)
                    VALUES (1, 1, 'jdoe', 'John', NULL, 'Doe', 1, '{$this->createdDate->format('Y-m-d H:i:s')}', NULL),
                      (2, 1, 'janedoe', 'Jane', NULL, 'Doe', 0, '{$this->createdDate->format('Y-m-d H:i:s')}', '{$this->createdDate->format('Y-m-d H:i:s')}'),
                      (3, 1, 'bobthecow', 'Bob', 'the', 'Cow', 1, '{$this->createdDate->format('Y-m-d H:i:s')}', NULL)");
        $pdo->exec("CREATE TABLE `user_group` (
                      user_id  INTEGER NOT NULL,
                      group_id INTEGER NOT NULL,
                      CONSTRAINT user_group_user_id_group_id_pk PRIMARY KEY (user_id, group_id)
                    );");
        $pdo->exec("INSERT INTO `user_group` (user_id, group_id) 
                    VALUES (1, 2)");
        $pdo->exec("CREATE TABLE `group` (
                      id    INTEGER PRIMARY KEY,
                      name       NOT NULL,
                      title TEXT NOT NULL
                    )");
        $pdo->exec("INSERT INTO `group` (id, name, title)
                    VALUES (1, 'test_name', 'Test Title'),
                      (2, 'test_name_2', 'Test Title 2');");

        return $pdo;
    }

    /**
     * @param Connection|null $connection
     * @return EntityManager
     */
    public function entityManager(Connection $connection = null)
    {
        return new EntityManager($connection ?: $this->connection());
    }

    /**
     * @param Connection|null $connection
     * @param array $methods
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    public function mockedEntityManager(Connection $connection = null, array $methods = [])
    {
        return $this->case
            ->getMockBuilder(EntityManager::class)
            ->setMethods($methods)
            ->setConstructorArgs([$connection ?: $this->connection()])
            ->getMock();
    }

    /**
     * @param string $entity
     * @return Repository
     */
    public function repository(string $entity)
    {
        return $this->callMethod($entity, 'repository');
    }

    public function mockedRepository(string $entity, EntityManager $entityManager = null, array $methods = [])
    {
        return $this->case
            ->getMockBuilder(Repository::class)
            ->setMethods($methods)
            ->setConstructorArgs([$entityManager ?: $this->entityManager(), $entity])
            ->getMock();
    }

    public function callMethod($class, $method, ...$arguments)
    {
        $reflectionClass  = new \ReflectionClass($class);
        $reflectionMethod = $reflectionClass->getMethod($method);
        $reflectionMethod->setAccessible(true);

        if (is_object($class)) {
            return $reflectionMethod->invoke($class, $arguments);
        }

        return $reflectionMethod->invoke(null, $arguments);
    }
}
