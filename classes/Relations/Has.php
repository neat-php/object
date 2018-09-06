<?php

namespace Neat\Object\Relations;

use Neat\Object\Policy;
use Neat\Object\Property;
use Neat\Object\Repository;

abstract class Has
{
    /**
     * @var Policy
     */
    protected $policy;

    /**
     * @var string
     */
    protected $localClass;

    /**
     * @var mixed|object
     */
    protected $local;

    /**
     * @var Property
     */
    protected $identifier;

    /**
     * @var Repository
     */
    protected $remoteRepository;

    /**
     * @var string
     */
    protected $foreignKeyColumn;

    /**
     * @var Property
     */
    protected $foreignKeyProperty;

    /**
     * HasOne constructor.
     * @param mixed|object $local
     * @param Policy $policy
     * @param Repository $remoteRepository
     * @param string|null $foreignKey actually this should always be determined by the Policy
     * @param Property|null $identifier same as $foreignKey
     */
    public function __construct(
        $local,
        Policy $policy,
        Repository $remoteRepository,
        string $foreignKey = null,
        Property $identifier = null
    ) {
        $this->localClass       = get_class($local);
        $this->local            = $local;
        $this->policy           = $policy;
        $this->remoteRepository = $remoteRepository;
        $this->foreignKeyColumn = $foreignKey ?: $policy->foreignKey($this->localClass);
        $this->identifier       = $identifier ?: $this->identifier($this->localClass);
    }

    /**
     * @param string $class
     * @return Property
     */
    protected function identifier($class)
    {
        $properties = $this->policy->properties($class);
        $keys       = $this->policy->key($class);
        $key        = array_shift($keys);

        return $properties[$key];
    }

    /**
     * @param mixed|object $entity
     * @return Property
     */
    protected function foreignKey($entity)
    {
        if (!$this->foreignKeyProperty) {
            $properties               = $this->policy->properties(get_class($entity));
            $this->foreignKeyProperty = $properties[$this->foreignKeyColumn];
        }

        return $this->foreignKeyProperty;
    }

    /**
     * @param mixed|object $entity
     */
    protected function setForeignKey($entity)
    {
        $this->foreignKey($entity)->set($entity, $this->identifier->get($this->local));
    }

    /**
     * @param mixed|object $entity
     */
    protected function unsetForeignKey($entity)
    {
        $this->foreignKey($entity)->set($entity, null);
        $this->remoteRepository->store($entity);
    }
}
