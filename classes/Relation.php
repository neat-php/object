<?php

namespace Neat\Object;

use Neat\Object\Relations\HasMany;
use Neat\Object\Relations\HasOne;

class Relation
{
    /**
     * @var Manager
     */
    private $manager;

    /**
     * @var Policy
     */
    private $policy;

    /**
     * Relation constructor.
     * @param Manager $manager
     * @param Policy $policy
     */
    public function __construct(Manager $manager, Policy $policy)
    {
        $this->manager = $manager;
        $this->policy  = $policy;
    }

    public function hasOne($local, string $remote)
    {
        return new HasOne($local, $this->policy, $this->manager->repository($remote));
    }

    public function hasMany($local, string $remote)
    {
        return new HasMany($local, $this->policy, $this->manager->repository($remote));
    }

    public function belongsToOne($local, string $remote)
    {

    }

    public function belongsToMany($local, string $remote)
    {

    }
}
