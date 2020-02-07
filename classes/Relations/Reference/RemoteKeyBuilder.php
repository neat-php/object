<?php

namespace Neat\Object\Relations\Reference;

use Neat\Object\Manager;
use Neat\Object\Policy;
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

    /**
     * RemoteKeyBuilder constructor.
     * @param Manager $manager
     * @param Policy  $policy
     * @param string  $local
     * @param string  $remote
     */
    public function __construct(Manager $manager, Policy $policy, string $local, string $remote)
    {
        $this->init($manager, $policy, RemoteKey::class);
        $localKey         = $policy->key($local);
        $foreignKey       = $policy->foreignKey($local);
        $localProperties  = $policy->properties($local);
        $remoteProperties = $policy->properties($remote);

        $this->localKey         = $localProperties[reset($localKey)];
        $this->remoteForeignKey = $remoteProperties[$foreignKey];
        $this->remoteKey        = $foreignKey;
        $this->remoteRepository = $manager->repository($remote);
    }

    protected function build(): Reference
    {
        return new $this->class($this->localKey, $this->remoteForeignKey, $this->remoteKey, $this->remoteRepository);
    }
}
