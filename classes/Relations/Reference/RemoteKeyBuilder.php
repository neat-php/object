<?php

namespace Neat\Object\Relations\Reference;

use Neat\Object\Manager;
use Neat\Object\Relations\Reference;
use Neat\Object\Relations\ReferenceBuilder;

class RemoteKeyBuilder implements ReferenceBuilder
{
    use Builder;

    /**
     * @param Manager      $manager
     * @param class-string $localClass
     * @param class-string $remoteClass
     */
    public function __construct(Manager $manager, string $localClass, string $remoteClass)
    {
        $this->init($manager, RemoteKey::class, $localClass, $remoteClass);
        $this->initLocalKeyColumn($this->keyColumn($localClass));
        $this->initRemoteKeyColumn($this->foreignKeyColumn($localClass));
    }

    protected function build(): Reference
    {
        return new $this->class(
            $this->localKeyProperty,
            $this->remoteKeyProperty,
            $this->remoteKeyColumn,
            $this->remoteRepository
        );
    }
}
