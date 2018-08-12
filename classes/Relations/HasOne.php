<?php

namespace Neat\Object\Relations;

use Neat\Object\Entity;

class HasOne extends Has
{
    /**
     * @var mixed|Entity|object
     */
    protected $remote;

    /**
     * @return mixed|Entity|object
     */
    public function get()
    {
        if (!$this->remote) {
            $identifier = $this->identifier->get($this->local);
            $result     = $this->remoteRepository->findOne([$this->foreignKeyColumn => $identifier]);

            $this->remote = $result;
        }

        return $this->remote;
    }

    /**
     * @param mixed|Entity|object $entity
     * @return $this
     */
    public function set($entity)
    {
        $this->remote = $entity;
        $this->setForeignKey($entity);

        return $this;
    }

    protected function unset($entity)
    {

    }

    public function store()
    {
        if (!$this->remote) {
            return;
        }

        $this->remoteRepository->store($this->remote);
    }
}
