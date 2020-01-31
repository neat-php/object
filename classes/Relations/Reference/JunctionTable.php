<?php

namespace Neat\Object\Relations\Reference;

use Neat\Database\Connection;
use Neat\Object\Property;
use Neat\Object\Query;
use Neat\Object\Relations\Reference;
use Neat\Object\RepositoryInterface;

class JunctionTable extends Reference
{
    private Property $localKey;

    private Property $remoteKey;

    private string $remoteKeyString;

    private RepositoryInterface $remoteRepository;

    private Connection $connection;

    private string $table;

    private string $localForeignKey;

    private string $remoteForeignKey;

    public function __construct(
        Property $localKey,
        Property $remoteKey,
        string $remoteKeyString,
        RepositoryInterface $remoteRepository,
        Connection $connection,
        string $junctionTable,
        string $localForeignKey,
        string $remoteForeignKey
    ) {
        $this->localKey         = $localKey;
        $this->remoteKey        = $remoteKey;
        $this->remoteKeyString  = $remoteKeyString;
        $this->remoteRepository = $remoteRepository;
        $this->connection       = $connection;
        $this->table            = $junctionTable;
        $this->localForeignKey  = $localForeignKey;
        $this->remoteForeignKey = $remoteForeignKey;
    }

    /**
     * @param object $local
     * @return object[]
     */
    public function load(object $local): array
    {
        return $this->select($local)->all();
    }

    /**
     *
     * @param object $local
     * @return Query
     */
    public function select(object $local): Query
    {
        return $this->remoteRepository
            ->select('rt')
            ->innerJoin($this->table, 'jt', "rt.{$this->remoteKeyString} = jt.{$this->remoteForeignKey}")
            ->where([$this->localForeignKey => $this->localKey->get($local)]);
    }

    /**
     * @param object   $local
     * @param object[] $remotes
     * @return void
     */
    public function store(object $local, array $remotes): void
    {
        $localIdentifier = $this->localKey->get($local);

        $after = array_map(
            function ($remote) use ($localIdentifier) {
                return [
                    $this->localForeignKey  => (string)$localIdentifier,
                    $this->remoteForeignKey => (string)$this->remoteKey->get($remote),
                ];
            },
            $remotes
        );

        $before = $this->connection
            ->select('*')
            ->from($this->table)
            ->where([$this->localForeignKey => $localIdentifier])
            ->query()
            ->rows();

        $delete = $this->diff($before, $after, [$this, 'compare']);
        $insert = $this->diff($after, $before, [$this, 'compare']);
        foreach ($delete as $row) {
            $this->connection->delete($this->table, $row);
        }
        foreach ($insert as $row) {
            $this->connection->insert($this->table, $row);
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
            static function ($itemA) use ($b, $compare) {
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
     * @param object $remote
     * @return mixed
     */
    public function getRemoteKeyValue(object $remote)
    {
        return $this->remoteKey->get($remote);
    }
}
