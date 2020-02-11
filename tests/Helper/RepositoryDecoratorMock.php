<?php

namespace Neat\Object\Test\Helper;

use Neat\Object\Repository\RepositoryDecorator;
use Neat\Object\RepositoryInterface;

class RepositoryDecoratorMock
{
    use RepositoryDecorator;

    /** @var RepositoryInterface */
    private $repository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    protected function repository(): RepositoryInterface
    {
        return $this->repository;
    }
}
