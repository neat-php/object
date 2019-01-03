<?php

namespace Neat\Object\Relations\Reference;

use Neat\Object\Property;
use Neat\Object\Query;
use Neat\Object\Relations\Reference;
use Neat\Object\RepositoryInterface;

class RemoteKey extends Reference
{
    /**
     * @var Property
     */
    private $localKey;

    /**
     * @var Property
     */
    private $remoteForeignKey;

    /**
     * @var string
     */
    private $remoteKey;

    /**
     * @var RepositoryInterface
     */
    private $remoteRepository;

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

    public function store($local, array $remotes)
    {
        /**
         * @param object $entityA
         * @param object $entityB
         * @return bool
         */
        $compare = function ($entityA, $entityB): bool {
            return $this->remoteRepository->identifier($entityA) == $this->remoteRepository->identifier($entityB);
        };

        $id      = $this->localKey->get($local);
        $current = $this->load($local);
        $new     = $remotes;
        $delete  = array_udiff($current, $new, $compare);
        $insert  = array_udiff($new, $current, $compare);
        $update  = array_uintersect($new, $current, $compare);
        foreach ($insert as $remote) {
            $this->remoteForeignKey->set($remote, $id);
            $this->remoteRepository->store($remote);
        }
        foreach ($delete as $remote) {
            // Do we really want to delete the record or just unset the remote key
            $this->remoteRepository->delete($remote);
        }
        foreach ($update as $remote) {
            $this->remoteRepository->store($remote);
        }
    }

    /**
     * @param $remote
     * @return mixed
     */
    public function getRemoteKeyValue($remote)
    {
        return $this->remoteForeignKey->get($remote);
    }
}
