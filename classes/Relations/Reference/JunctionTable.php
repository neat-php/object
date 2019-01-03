<?php

namespace Neat\Object\Relations\Reference;

use Neat\Database\Connection;
use Neat\Object\Property;
use Neat\Object\Query;
use Neat\Object\Relations\Reference;
use Neat\Object\RepositoryInterface;

class JunctionTable extends Reference
{
    /**
     * @var Property
     */
    private $localKey;

    /**
     * @var Property
     */
    private $remoteKey;

    /**
     * @var string
     */
    private $remoteKeyString;

    /**
     * @var RepositoryInterface
     */
    private $remoteRepository;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $table;

    /**
     * @var string
     */
    private $localForeignKey;

    /**
     * @var string
     */
    private $remoteForeignKey;

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
    public function load($local): array
    {
        return $this->select($local)->all();
    }

    /**
     *
     * @param object $local
     * @return Query
     */
    public function select($local): Query
    {
        return $this->remoteRepository
            ->select('rt')
            ->innerJoin($this->table, 'jt', "rt.$this->remoteKeyString = jt.$this->remoteForeignKey")
            ->where([$this->localForeignKey => $this->localKey->get($local)]);
    }

    /**
     * @param object   $local
     * @param object[] $remotes
     * @return void
     */
    public function store($local, array $remotes)
    {
        $localIdentifier = $this->localKey->get($local);

        $new = array_map(function ($remote) use ($localIdentifier) {
            return [
                $this->localForeignKey  => (string)$localIdentifier,
                $this->remoteForeignKey => (string)$this->remoteKey->get($remote),
            ];
        }, $remotes);

        $current = $this->connection
            ->select('*')
            ->from($this->table)
            ->where([$this->localForeignKey => $localIdentifier])
            ->query()
            ->rows();

        $delete = $this->diff($current, $new, [$this, 'compare']);
        $insert = $this->diff($new, $current, [$this, 'compare']);
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
        return array_filter($a, function ($itemA) use ($b, $compare) {
            foreach ($b as $itemB) {
                if ($compare($itemA, $itemB)) {
                    return false;
                }
            }

            return true;
        });
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
     * @param $remote
     * @return mixed
     */
    public function getRemoteKeyValue($remote)
    {
        return $this->remoteKey->get($remote);
    }
}
