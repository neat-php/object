<?php

namespace Neat\Object\Test\Helper;

use Neat\Object\Manager;
use Neat\Object\Policy;
use Neat\Object\Relations\Reference\Builder;

abstract class ReferenceBuilderMock
{
    use Builder;

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
