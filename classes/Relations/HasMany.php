<?php

namespace Neat\Object\Relations;

use Neat\Object\Collection;
use Neat\Object\Policy;
use Neat\Object\Repository;

class HasMany extends Has
{
    /**
     * @var mixed[]|object[]
     */
    protected $remotes;

    /**
     * @var string
     */
    protected $collection;

    /**
     * HasMany constructor.
     * @param mixed|object $local
     * @param Policy $policy
     * @param Repository $remoteRepository
     * @param string $collection
     */
    public function __construct(
        $local,
        Policy $policy,
        Repository $remoteRepository,
        string $collection = Collection::class
    ) {
        parent::__construct($local, $policy, $remoteRepository);
        $this->collection = $collection;
    }

    public function get()
    {
        if (!$this->remotes) {
            $identifier = $this->identifier->get($this->local);
            $result     = $this->remoteRepository->all([$this->foreignKeyColumn => $identifier]);

            $this->remotes = $result;
        }

        return $this->remotes;
    }

    /**
     * @param mixed[]|object[]|Collection $entities
     * @return HasMany
     */
    public function set($entities)
    {
        if ($entities instanceof Collection) {
            $entities = $entities->all();
        }
        $this->remotes = $entities;
        array_walk($this->remotes, [$this, 'setForeignKey']);

        return $this;
    }

    /**
     * @param mixed|object $entity
     * @return $this
     */
    public function add($entity)
    {
        $this->get();
        $this->remotes[] = $entity;
        $this->setForeignKey($entity);

        return $this;
    }

    /**
     * @param mixed|object $entity
     * @return $this
     */
    public function remove($entity)
    {
        foreach ($this->remotes as $key => $remote) {
            if ($this->isSameEntity($remote, $entity)) {
                unset($this->remotes[$key]);
                break;
            }
        }

        return $this;
    }

    public function store()
    {
        if (!$this->remotes) {
            return;
        }
        foreach ($this->remotes as $entity) {
            $this->remoteRepository->store($entity);
        }
    }

    /**
     * @param mixed|object $remote
     * @param mixed|object $entity
     * @return bool
     */
    protected function isSameEntity($remote, $entity)
    {
        return $this->getIdentifierValue($remote) == $this->getIdentifierValue($entity);
    }

    /**
     * @param mixed|object $entity
     * @return mixed
     */
    protected function getIdentifierValue($entity)
    {
        return $this->identifier($entity)->get($entity);
    }
}
