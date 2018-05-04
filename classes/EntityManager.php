<?php


namespace Neat\Object;


use Neat\Database\Connection;

class EntityManager
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getConnection(): Connection
    {
        return $this->connection;
    }

    /**
     * @param string $entity
     * @return mixed|Repository
     */
    public function getRepository(string $entity): Repository
    {
        $repositoryClass = Repository::class;
        if (defined("$entity::REPOSITORY")) {
            $repositoryClass = $entity::REPOSITORY;
        }

        return new $repositoryClass($this, $entity);
    }
}