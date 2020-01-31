<?php

namespace Neat\Object\Relations\Reference;

use Neat\Object\Property;
use Neat\Object\Query;
use Neat\Object\Relations\Reference;
use Neat\Object\RepositoryInterface;

class LocalKey extends Reference
{
    private Property $localForeignKey;

    private Property $remoteKey;

    private string $remoteKeyString;

    private RepositoryInterface $remoteRepository;

    public function __construct(
        Property $localForeignKey,
        Property $remoteKey,
        string $remoteKeyString,
        RepositoryInterface $remoteRepository
    ) {
        $this->localForeignKey  = $localForeignKey;
        $this->remoteKey        = $remoteKey;
        $this->remoteKeyString  = $remoteKeyString;
        $this->remoteRepository = $remoteRepository;
    }

    /**
     * @param object $local
     * @return object[]
     */
    public function load($local): array
    {
        $identifier = $this->localForeignKey->get($local);

        if (is_null($identifier)) {
            return [];
        }

        return $this->remoteRepository->all([$this->remoteKeyString => $identifier]);
    }

    /**
     *
     * @param object $local
     * @return Query
     */
    public function select($local): Query
    {
        $identifier = $this->localForeignKey->get($local);

        return $this->remoteRepository->select()->where([$this->remoteKeyString => $identifier]);
    }

    /**
     * @param object   $local
     * @param object[] $remotes
     * @return void
     */
    public function store($local, array $remotes): void
    {
        $remote = reset($remotes);
        if ($remote) {
            $this->localForeignKey->set($local, $this->remoteKey->get($remote));
        }
    }

    /**
     * @param object $remote
     * @return string|null
     */
    public function getRemoteKeyValue(object $remote): ?string
    {
        return $this->remoteKey->get($remote);
    }
}
