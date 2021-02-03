<?php

namespace Neat\Object\Relations\Reference;

use Neat\Object\Property;
use Neat\Object\Query;
use Neat\Object\Relations\Reference;
use Neat\Object\RepositoryInterface;

class RemoteKey extends Reference
{
    /** @var Property */
    private $localKeyProperty;

    /** @var Property */
    private $remoteKeyProperty;

    /** @var string */
    private $remoteKeyColumn;

    /** @var RepositoryInterface */
    private $remoteRepository;

    /**
     * RemoteKey constructor.
     *
     * @param Property            $localKeyProperty
     * @param Property            $remoteKeyProperty
     * @param string              $remoteKeyColumn
     * @param RepositoryInterface $remoteRepository
     */
    public function __construct(
        Property $localKeyProperty,
        Property $remoteKeyProperty,
        string $remoteKeyColumn,
        RepositoryInterface $remoteRepository
    ) {
        $this->localKeyProperty  = $localKeyProperty;
        $this->remoteKeyProperty = $remoteKeyProperty;
        $this->remoteKeyColumn   = $remoteKeyColumn;
        $this->remoteRepository  = $remoteRepository;
    }

    /**
     * @inheritDoc
     */
    public function load(object $local): array
    {
        return $this->select($local)->all();
    }

    /**
     * @inheritDoc
     */
    public function select(object $local): Query
    {
        $remoteKey = $this->localKeyProperty->get($local);

        return $this->remoteRepository->select()->where([$this->remoteKeyColumn => $remoteKey]);
    }

    /**
     * @inheritDoc
     */
    public function store(object $local, array $remotes): void
    {
        $id     = $this->localKeyProperty->get($local);
        $before = $this->load($local);
        $after  = $remotes;
        $diff   = new Diff($this->remoteRepository, $before, $after);
        foreach ($diff->getInsert() as $remote) {
            $this->remoteKeyProperty->set($remote, $id);
            $this->remoteRepository->store($remote);
        }
        foreach ($diff->getDelete() as $remote) {
            // Do we really want to delete the record or just unset the remote key
            $this->remoteRepository->delete($remote);
        }
        foreach ($diff->getUpdate() as $remote) {
            $this->remoteRepository->store($remote);
        }
    }

    /**
     * @inheritDoc
     */
    public function getRemoteKeyValue(object $remote): array
    {
        return $this->remoteRepository->identifier($remote);
    }
}
