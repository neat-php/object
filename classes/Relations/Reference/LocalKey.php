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
    private $remoteKeyColumn;

    /** @var RepositoryInterface */
    private $remoteRepository;

    /**
     * LocalKey constructor.
     *
     * @param Property            $localForeignKey
     * @param Property            $remoteKey
     * @param string              $remoteKeyColumn
     * @param RepositoryInterface $remoteRepository
     */
    public function __construct(
        Property $localForeignKey,
        Property $remoteKey,
        string $remoteKeyColumn,
        RepositoryInterface $remoteRepository
    ) {
        $this->localForeignKey  = $localForeignKey;
        $this->remoteKey        = $remoteKey;
        $this->remoteKeyColumn  = $remoteKeyColumn;
        $this->remoteRepository = $remoteRepository;
    }

    /**
     * @inheritDoc
     */
    public function load($local): array
    {
        $identifier = $this->localForeignKey->get($local);

        return is_null($identifier) ? [] : $this->select($local)->all();
    }

    /**
     * @inheritDoc
     */
    public function select($local): Query
    {
        $identifier = $this->localForeignKey->get($local);

        return $this->remoteRepository->select()->where([$this->remoteKeyColumn => $identifier]);
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
