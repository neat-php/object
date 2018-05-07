<?php

namespace Neat\Object\Test\Helper;

use Neat\Database\Connection;
use Neat\Object\EntityManager;
use Neat\Object\Repository;
use PDO;

class Factory extends \Neat\Database\Test\Factory
{
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
        $pdo->exec("INSERT INTO user (id, type_id, username, first_name, middle_name, last_name, active, update_date, deleted_date)
                    VALUES (1, 1, 'jdoe', 'John', NULL, 'Doe', 1, CURRENT_TIMESTAMP, null),
                      (2, 1, 'janedoe', 'Jane', NULL, 'Doe', 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
                      (3, 1, 'bobthecow', 'Bob', 'the', 'Cow', 1, CURRENT_TIMESTAMP, null)");

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

    public function repository(string $entity, EntityManager $entityManager = null)
    {
        return new Repository($entityManager ?: $this->entityManager(), $entity);
    }

    public function mockedRepository(string $entity, EntityManager $entityManager = null, array $methods = [])
    {
        return $this->case
            ->getMockBuilder(Repository::class)
            ->setMethods($methods)
            ->setConstructorArgs([$entityManager ?: $this->entityManager(), $entity])
            ->getMock();
    }
}
