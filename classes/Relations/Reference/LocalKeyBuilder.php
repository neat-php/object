<?php

namespace Neat\Object\Relations\Reference;

use Neat\Object\Manager;
use Neat\Object\Policy;
use Neat\Object\Property;
use Neat\Object\Relations\Reference;
use Neat\Object\Relations\ReferenceBuilder;
use Neat\Object\RepositoryInterface;

class LocalKeyBuilder implements ReferenceBuilder
{
    use Builder;

    /** @var Property */
    private $localKey;

    /** @var Property */
    private $remoteKey;

    /** @var string */
    private $remoteKeyString;

    /** @var RepositoryInterface */
    private $remoteRepository;

    public function __construct(Manager $manager, Policy $policy, string $local, string $remote)
    {
        $this->init($manager, $policy, LocalKey::class);
        $localForeignKey  = $policy->foreignKey($remote);
        $remoteKey        = $policy->key($remote);
        $localProperties  = $policy->properties($local);
        $remoteProperties = $policy->properties($remote);

        $this->localKey         = $localProperties[$localForeignKey];
        $this->remoteKey        = $remoteProperties[reset($remoteKey)];
        $this->remoteKeyString  = reset($remoteKey);
        $this->remoteRepository = $manager->repository($remote);
    }

    protected function build(): Reference
    {
        return new $this->class(
            $this->localKey,
            $this->remoteKey,
            $this->remoteKeyString,
            $this->remoteRepository
        );
    }

    /**
     * @param string $localKey
     * @return LocalKeyBuilder
     */
    public function setLocalKey(string $localKey): LocalKeyBuilder
    {
        $this->localKey = $localKey;

        return $this;
    }

    /**
     * @param Property $remoteKey
     * @return LocalKeyBuilder
     */
    public function setRemoteKey(Property $remoteKey): LocalKeyBuilder
    {
        $this->remoteKey = $remoteKey;

        return $this;
    }

    /**
     * @param string $remoteKeyString
     * @return LocalKeyBuilder
     */
    public function setRemoteKeyString(string $remoteKeyString): LocalKeyBuilder
    {
        $this->remoteKeyString = $remoteKeyString;

        return $this;
    }

    /**
     * @param RepositoryInterface $remoteRepository
     * @return LocalKeyBuilder
     */
    public function setRemoteRepository(RepositoryInterface $remoteRepository): LocalKeyBuilder
    {
        $this->remoteRepository = $remoteRepository;

        return $this;
    }
}
