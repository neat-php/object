<?php

namespace Neat\Object\Relations\Reference;

use Neat\Object\Manager;
use Neat\Object\Property;
use Neat\Object\Relations\Reference;
use Neat\Object\Relations\ReferenceBuilder;
use Neat\Object\RepositoryInterface;

class RemoteKeyBuilder implements ReferenceBuilder
{
    use Builder;

    /** @var Property */
    private $localKey;

    /** @var Property */
    private $remoteForeignKey;

    /** @var string */
    private $remoteKey;

    /** @var RepositoryInterface */
    private $remoteRepository;

    public function __construct(Manager $manager, string $local, string $remote)
    {
        $policy = $manager->policy();
        $this->init($manager, $policy, RemoteKey::class);
        $localKey         = $policy->key($local);
        $foreignKey       = $policy->foreignKey($local);
        $localProperties  = $policy->properties($local);
        $remoteProperties = $policy->properties($remote);

        $this->localKey         = $localProperties[reset($localKey)] ?? null;
        $this->remoteForeignKey = $remoteProperties[$foreignKey] ?? null;
        $this->remoteKey        = $foreignKey;
        $this->remoteRepository = $manager->repository($remote);
    }

    protected function build(): Reference
    {
        return new $this->class($this->localKey, $this->remoteForeignKey, $this->remoteKey, $this->remoteRepository);
    }

    /**
     * @param Property $localKey
     * @return $this
     */
    public function setLocalKey(Property $localKey): self
    {
        $this->localKey = $localKey;

        return $this;
    }

    /**
     * @param Property $remoteKey
     * @return $this
     */
    public function setRemoteKey(Property $remoteKey): self
    {
        $this->remoteForeignKey = $remoteKey;

        return $this;
    }

    /**
     * @param string $remoteKeyString
     * @return $this
     */
    public function setRemoteKeyString(string $remoteKeyString): self
    {
        $this->remoteKey = $remoteKeyString;

        return $this;
    }
}
