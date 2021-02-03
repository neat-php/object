<?php

namespace Neat\Object\Relations\Reference;

use Neat\Database\Connection;
use Neat\Object\Manager;
use Neat\Object\Relations\Reference;
use Neat\Object\Relations\ReferenceBuilder;

class JunctionTableBuilder implements ReferenceBuilder
{
    use Builder;

    /** @var Connection */
    private $connection;

    /** @var string */
    private $junctionTable;

    /** @var string */
    private $junctionTableLocalKeyColumn;

    /** @var string */
    private $junctionTableRemoteKeyColumn;

    /**
     * @param Manager      $manager
     * @param class-string $localClass
     * @param class-string $remoteClass
     */
    public function __construct(Manager $manager, string $localClass, string $remoteClass)
    {
        $this->init($manager, JunctionTable::class, $localClass, $remoteClass);
        $this->initLocalKeyColumn($this->keyColumn($localClass));
        $this->initRemoteKeyColumn($this->keyColumn($remoteClass));

        $this->connection                   = $manager->connection();
        $this->junctionTable                = $this->junctionTable($localClass, $remoteClass);
        $this->junctionTableLocalKeyColumn  = $this->foreignKeyColumn($localClass);
        $this->junctionTableRemoteKeyColumn = $this->foreignKeyColumn($remoteClass);
    }

    /**
     * @inheritDoc
     */
    protected function build(): Reference
    {
        return new $this->class(
            $this->localKeyProperty,
            $this->remoteKeyProperty,
            $this->remoteKeyColumn,
            $this->remoteRepository,
            $this->connection,
            $this->junctionTable,
            $this->junctionTableLocalKeyColumn,
            $this->junctionTableRemoteKeyColumn
        );
    }

    /**
     * @param class-string $localClass
     * @param class-string $remoteClass
     * @return string
     */
    public function junctionTable(string $localClass, string $remoteClass): string
    {
        return $this->policy->junctionTable($localClass, $remoteClass);
    }

    /**
     * @param string $junctionTable
     * @return self
     */
    public function setJunctionTable(string $junctionTable): self
    {
        $this->junctionTable = $junctionTable;

        return $this;
    }

    /**
     * @param string $junctionTableLocalKeyColumn
     * @return self
     */
    public function setJunctionTableLocalKeyColumn(string $junctionTableLocalKeyColumn): self
    {
        $this->junctionTableLocalKeyColumn = $junctionTableLocalKeyColumn;

        return $this;
    }

    /**
     * @param string $junctionTableRemoteKeyColumn
     * @return self
     */
    public function setJunctionTableRemoteKeyColumn(string $junctionTableRemoteKeyColumn): self
    {
        $this->junctionTableRemoteKeyColumn = $junctionTableRemoteKeyColumn;

        return $this;
    }
}
