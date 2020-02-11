<?php

namespace Neat\Object\Test\Helper;

use Neat\Object\Manager;
use Neat\Object\Policy;
use Neat\Object\Reference\Builder;
use Neat\Object\Reference\Reference;

abstract class ReferenceBuilderMock
{
    use Builder;

    /**
     * @return Reference|null
     */
    public function getResolved(): Reference
    {
        return $this->resolved;
    }

    /**
     * @param Reference|null $resolved
     */
    public function setResolved(Reference $resolved)
    {
        $this->resolved = $resolved;
    }

    /**
     * @return callable|null
     */
    public function getFactory(): callable
    {
        return $this->factory;
    }

    /**
     * @param callable|null $factory
     */
    public function setFactory(callable $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @return Manager
     */
    public function getManager(): Manager
    {
        return $this->manager;
    }

    /**
     * @param Manager $manager
     */
    public function setManager(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @return Policy
     */
    public function getPolicy(): Policy
    {
        return $this->policy;
    }

    /**
     * @param Policy $policy
     */
    public function setPolicy(Policy $policy)
    {
        $this->policy = $policy;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }
}
