<?php

namespace Neat\Object\Decorator;

use Neat\Object\Event;
use Neat\Object\RepositoryDecorator;
use Neat\Object\RepositoryInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

class EventDispatcher implements RepositoryInterface
{
    use RepositoryDecorator;

    /** @var RepositoryInterface */
    protected $repository;

    /** @var EventDispatcherInterface */
    protected $dispatcher;

    /** @var string[] */
    protected $events;

    /**
     * EventDispatcher constructor
     *
     * @param RepositoryInterface      $repository
     * @param EventDispatcherInterface $dispatcher
     * @param string[]                 $events
     */
    public function __construct(RepositoryInterface $repository, EventDispatcherInterface $dispatcher, array $events)
    {
        $this->repository = $repository;
        $this->dispatcher = $dispatcher;
        $this->events     = $events;
    }

    /**
     * @return RepositoryInterface
     */
    protected function repository(): RepositoryInterface
    {
        return $this->repository;
    }

    /**
     * Trigger event for an entity
     *
     * @param class-string $event
     * @param object       $entity
     * @return void
     */
    public function trigger(string $event, object $entity): void
    {
        if ($class = $this->events[$event] ?? null) {
            $instance = new $class($entity);

            $this->dispatcher->dispatch($instance);
        }
    }

    /**
     * @inheritDoc
     */
    public function load($entity)
    {
        $this->trigger(Event::LOADING, $entity);

        $result = $this->repository()->load($entity);

        $this->trigger(Event::LOADED, $result);

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function store($entity)
    {
        $identifier = $this->identifier($entity);
        $exists     = $identifier && array_filter($identifier) && $this->has($identifier);

        if ($exists) {
            $this->trigger(Event::UPDATING, $entity);
        } else {
            $this->trigger(Event::CREATING, $entity);
        }

        $this->repository()->store($entity);

        if ($exists) {
            $this->trigger(Event::UPDATED, $entity);
        } else {
            $this->trigger(Event::CREATED, $entity);
        }
    }

    /**
     * @inheritDoc
     */
    public function delete($entity)
    {
        $this->trigger(Event::DELETING, $entity);

        $deleted = $this->repository()->delete($entity);

        $this->trigger(Event::DELETED, $entity);

        return $deleted;
    }
}
