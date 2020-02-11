<?php

namespace Neat\Object\Reference;

use Neat\Object\Manager;
use Neat\Object\Property;
use Neat\Object\Reference;
use Neat\Object\Repository;

class LocalKeyBuilder implements ReferenceBuilder
{
    use Builder;

    /** @var Property */
    private $localKey;

    /** @var Property */
    private $remoteKey;

    /** @var string */
    private $remoteKeyString;

    /** @var Repository */
    private $remoteRepository;

    /**
     * LocalKeyBuilder constructor
     *
     * @param Manager $manager
     * @param string  $local
     * @param string  $remote
     */
    public function __construct(Manager $manager, string $local, string $remote)
    {
        $policy = $manager->policy();
        $this->init($manager, $policy, LocalKey::class);
        $localForeignKey  = $policy->foreignKey($remote);
        $remoteKey        = $policy->key($remote);
        $localProperties  = $policy->properties($local);
        $remoteProperties = $policy->properties($remote);

        $this->localKey         = $localProperties[$localForeignKey] ?? null;
        $this->remoteKey        = $remoteProperties[reset($remoteKey)] ?? null;
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
     * @param Property $localKey
     * @return LocalKeyBuilder
     */
    public function setLocalKey(Property $localKey): LocalKeyBuilder
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
     * @param Repository $remoteRepository
     * @return LocalKeyBuilder
     */
    public function setRemoteRepository(Repository $remoteRepository): LocalKeyBuilder
    {
        $this->remoteRepository = $remoteRepository;

        return $this;
    }
}
