<?php

namespace Neat\Object\Reference;

use Neat\Database\Connection;
use Neat\Object\Property;
use Neat\Object\Query;
use Neat\Object\Reference;
use Neat\Object\Repository;

class JunctionTable implements Reference
{
    /** @var Property */
    private $localKey;

    /** @var Property */
    private $remoteKey;

    /** @var string */
    private $remoteKeyColumn;

    /** @var Repository */
    private $remoteRepository;

    /** @var Connection */
    private $connection;

    /** @var string */
    private $junctionTable;

    /** @var string */
    private $junctionTableLocalForeignKey;

    /** @var string */
    private $junctionTableRemoteForeignKey;

    /**
     * JunctionTable constructor.
     *
     * @param Property   $localKey
     * @param Property   $remoteKey
     * @param string     $remoteKeyString
     * @param Repository $remoteRepository
     * @param Connection $connection
     * @param string     $junctionTable
     * @param string     $localForeignKey
     * @param string     $remoteForeignKey
     */
    public function __construct(
        Property $localKey,
        Property $remoteKey,
        string $remoteKeyString,
        Repository $remoteRepository,
        Connection $connection,
        string $junctionTable,
        string $localForeignKey,
        string $remoteForeignKey
    ) {
        $this->localKey                      = $localKey;
        $this->remoteKey                     = $remoteKey;
        $this->remoteKeyColumn               = $remoteKeyString;
        $this->remoteRepository              = $remoteRepository;
        $this->connection                    = $connection;
        $this->junctionTable                 = $junctionTable;
        $this->junctionTableLocalForeignKey  = $localForeignKey;
        $this->junctionTableRemoteForeignKey = $remoteForeignKey;
    }

    /**
     * @inheritDoc
     */
    public function load($local): array
    {
        return $this->select($local)->all();
    }

    /**
     * @inheritDoc
     */
    public function select($local): Query
    {
        return $this->remoteRepository
            ->select('rt')
            ->innerJoin(
                $this->junctionTable,
                'jt',
                "rt.$this->remoteKeyColumn = jt.$this->junctionTableRemoteForeignKey"
            )
            ->where([$this->junctionTableLocalForeignKey => $this->localKey->get($local)]);
    }

    /**
     * @inheritDoc
     */
    public function store($local, array $remotes)
    {
        $localIdentifier = $this->localKey->get($local);

        $after = array_map(
            function ($remote) use ($localIdentifier) {
                return [
                    $this->junctionTableLocalForeignKey  => (string) $localIdentifier,
                    $this->junctionTableRemoteForeignKey => (string) $this->remoteKey->get($remote),
                ];
            },
            $remotes
        );

        $before = $this->connection
            ->select('*')
            ->from($this->junctionTable)
            ->where([$this->junctionTableLocalForeignKey => $localIdentifier])
            ->query()
            ->rows();

        $delete = $this->diff($before, $after, [$this, 'compare']);
        $insert = $this->diff($after, $before, [$this, 'compare']);
        foreach ($delete as $row) {
            $this->connection->delete($this->junctionTable, $row);
        }
        foreach ($insert as $row) {
            $this->connection->insert($this->junctionTable, $row);
        }
    }

    /**
     * Should return all items of $a that are not present in $b
     *
     * @param array    $a
     * @param array    $b
     * @param callable $compare
     * @return array
     */
    public function diff(array $a, array $b, callable $compare): array
    {
        return array_filter(
            $a,
            function ($itemA) use ($b, $compare) {
                foreach ($b as $itemB) {
                    if ($compare($itemA, $itemB)) {
                        return false;
                    }
                }

                return true;
            }
        );
    }

    /**
     * Should return false if $itemA doesn't match $itemB
     *
     * @param array $itemA
     * @param array $itemB
     * @return bool
     */
    public function compare(array $itemA, array $itemB): bool
    {
        foreach ($itemA as $index => $item) {
            if (!isset($itemB[$index]) || $itemB[$index] != $item) {
                return false;
            }
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function getRemoteKeyValue($remote)
    {
        return $this->remoteRepository->identifier($remote);
    }
}
