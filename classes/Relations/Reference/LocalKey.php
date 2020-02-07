<?php

namespace Neat\Object\Relations\Reference;

use Neat\Object\Property;
use Neat\Object\Query;
use Neat\Object\Relations\Reference;
use Neat\Object\RepositoryInterface;

class LocalKey extends Reference
{
    /** @var Property */
    private $localForeignKey;

    /** @var Property */
    private $remoteKey;

    /** @var string */
    private $remoteKeyString;

    /** @var RepositoryInterface */
    private $remoteRepository;

    /**
     * LocalKey constructor.
     * @param Property            $localForeignKey
     * @param Property            $remoteKey
     * @param string              $remoteKeyString
     * @param RepositoryInterface $remoteRepository
     */
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
     * @inheritDoc
     */
    public function load($local): array
    {
        $identifier = $this->localForeignKey->get($local);

        return is_null($identifier) ? [] : $this->remoteRepository->all([$this->remoteKeyString => $identifier]);
    }

    /**
     * @inheritDoc
     */
    public function select($local): Query
    {
        $identifier = $this->localForeignKey->get($local);

        return $this->remoteRepository->select()->where([$this->remoteKeyString => $identifier]);
    }

    /**
     * @inheritDoc
     */
    public function store($local, array $remotes)
    {
        $remote = reset($remotes);
        if ($remote) {
            $this->localForeignKey->set($local, $this->remoteKey->get($remote));
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
