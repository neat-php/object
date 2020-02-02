<?php

namespace Neat\Object\Relations\Reference;

use Neat\Object\Property;
use Neat\Object\Query;
use Neat\Object\Relations\Reference;
use Neat\Object\RepositoryInterface;

class LocalKey extends Reference
{
    /**
     * @var Property
     */
    private $localForeignKey;

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

        return is_null($identifier) ? [] : $this->remoteRepository->all([$this->remoteKeyString => $identifier]);
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
    public function store($local, array $remotes)
    {
        if (($remote = reset($remotes))) {
            $this->localForeignKey->set($local, $this->remoteKey->get($remote));
        }
    }

    /**
     * @param $remote
     * @return mixed
     */
    public function getRemoteKeyValue($remote)
    {
        return $this->remoteRepository->identifier($remote);
    }
}
