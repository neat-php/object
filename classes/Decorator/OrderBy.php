<?php

namespace Neat\Object\Decorator;

use Neat\Database\Query as QueryBuilder;
use Neat\Object\RepositoryDecorator;
use Neat\Object\RepositoryInterface;

class OrderBy implements RepositoryInterface
{
    use RepositoryDecorator;

    /** @var RepositoryInterface */
    protected $repository;

    /** @var string */
    protected $orderBy;

    public function __construct(RepositoryInterface $repository, string $orderBy)
    {
        $this->repository = $repository;
        $this->orderBy    = $orderBy;
    }

    protected function repository(): RepositoryInterface
    {
        return $this->repository;
    }

    public function query($conditions = null): QueryBuilder
    {
        return $this->repository()->query($conditions)->orderBy($this->orderBy);
    }
}
