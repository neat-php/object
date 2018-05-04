<?php


namespace Neat\Object\Test;

use Neat\Database\Connection;
use Neat\Object\EntityManager;
use Neat\Object\Repository;

class Factory extends \Neat\Database\Test\Factory
{
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
            ->setConstructorArgs([$entity, $entityManager ?: $this->entityManager()])
            ->getMock();
    }
}