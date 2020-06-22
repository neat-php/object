<?php

namespace Neat\Object\Reference;

use Neat\Object\Property;
use Neat\Object\Query;
use Neat\Object\Reference;
use Neat\Object\Repository;

class RemoteKey implements Reference
{
    /** @var Property */
    private $localKey;

    /** @var Property */
    private $remoteForeignKey;

    /** @var string */
    private $remoteKey;

    /** @var Repository */
    private $remoteRepository;

    /**
     * RemoteKey constructor.
     *
     * @param Property   $localKey
     * @param Property   $remoteForeignKey
     * @param string     $remoteKey
     * @param Repository $remoteRepository
     */
    public function __construct(
        Property $localKey,
        Property $remoteForeignKey,
        string $remoteKey,
        Repository $remoteRepository
    ) {
        $this->localKey         = $localKey;
        $this->remoteForeignKey = $remoteForeignKey;
        $this->remoteKey        = $remoteKey;
        $this->remoteRepository = $remoteRepository;
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
        $remoteKey = $this->localKey->get($local);

        return $this->remoteRepository->select()->where([$this->remoteKey => $remoteKey]);
    }

    /**
     * @inheritDoc
     */
    public function store($local, array $remotes)
    {
        $id     = $this->localKey->get($local);
        $before = $this->load($local);
        $after  = $remotes;
        $diff   = new Diff($this->remoteRepository, $before, $after);
        foreach ($diff->getInsert() as $remote) {
            $this->remoteForeignKey->set($remote, $id);
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
    public function getRemoteKeyValue($remote)
    {
        return $this->remoteRepository->identifier($remote);
    }
}
