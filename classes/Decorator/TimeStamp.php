<?php

namespace Neat\Object\Decorator;

use Neat\Object\Property;
use Neat\Object\RepositoryDecorator;
use Neat\Object\RepositoryInterface;

abstract class TimeStamp implements RepositoryInterface
{
    use RepositoryDecorator;

    protected RepositoryInterface $repository;

    protected string $column;

    protected Property $property;

    public function __construct(RepositoryInterface $repository, string $column, Property $property)
    {
        $this->repository = $repository;
        $this->column     = $column;
        $this->property   = $property;
    }

    protected function repository(): RepositoryInterface
    {
        return $this->repository;
    }
}
