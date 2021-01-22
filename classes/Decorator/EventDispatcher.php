<?php

namespace Neat\Object\Decorator;

use Neat\Object\Event;
use Neat\Object\Exception\EventNotDefinedException;
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
     * @throws EventNotDefinedException In case the event is not defined on the entity.
     */
    public function trigger(string $event, object $entity): void
    {
        if (!isset($this->events[$event])) {
            $entityClass = get_class($entity);

            throw new EventNotDefinedException("No event defined for '{$entityClass}' named '{$event}'");
        }
        $this->dispatch($this->events[$event], $entity);
    }

    /**
     * Trigger event for an entity if the entity defines the event.
     *
     * @param string $event
     * @param object $entity
     * @return void
     */
    public function triggerIfExists(string $event, object $entity): void
    {
        if (!isset($this->events[$event])) {
            return;
        }
        $this->dispatch($this->events[$event], $entity);
    }

    private function dispatch(string $class, object $entity): void
    {
        $instance = new $class($entity);

        $this->dispatcher->dispatch($instance);
    }

    /**
     * @inheritDoc
     */
    public function load($entity)
    {
        $this->triggerIfExists(Event::LOADING, $entity);

        $result = $this->repository()->load($entity);

        $this->triggerIfExists(Event::LOADED, $result);

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
            $this->triggerIfExists(Event::UPDATING, $entity);
        } else {
            $this->triggerIfExists(Event::CREATING, $entity);
        }

        $this->repository()->store($entity);

        if ($exists) {
            $this->triggerIfExists(Event::UPDATED, $entity);
        } else {
            $this->triggerIfExists(Event::CREATED, $entity);
        }
    }

    /**
     * @inheritDoc
     */
    public function delete($entity)
    {
        $this->triggerIfExists(Event::DELETING, $entity);

        $deleted = $this->repository()->delete($entity);

        $this->triggerIfExists(Event::DELETED, $entity);

        return $deleted;
    }
}
