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
        // @TODO make it possible to overwrite the used repository for a specified Entity

        return new Repository($this, $entity);
    }
}