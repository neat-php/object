<?php

namespace Neat\Object\Relations\Reference;

use Neat\Object\Property;
use Neat\Object\Query;
use Neat\Object\Relations\Reference;
use Neat\Object\RepositoryInterface;

class RemoteKey extends Reference
{
    private Property $localKey;

    private Property $remoteForeignKey;

    private string $remoteKey;

    private RepositoryInterface $remoteRepository;

    public function __construct(
        Property $localKey,
        Property $remoteForeignKey,
        string $remoteKey,
        RepositoryInterface $remoteRepository
    ) {

        $this->localKey         = $localKey;
        $this->remoteForeignKey = $remoteForeignKey;
        $this->remoteKey        = $remoteKey;
        $this->remoteRepository = $remoteRepository;
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
            ->select()
            ->where([$this->remoteKey => $this->localKey->get($local)]);
    }

    public function store($local, array $remotes): void
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
     * @param object $remote
     * @return mixed
     */
    public function getRemoteKeyValue(object $remote)
    {
        return $this->remoteForeignKey->get($remote);
    }
}
