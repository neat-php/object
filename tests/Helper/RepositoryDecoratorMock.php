<?php

namespace Neat\Object\Test\Helper;

use Neat\Object\RepositoryDecorator;
use Neat\Object\RepositoryInterface;

class RepositoryDecoratorMock implements RepositoryInterface
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
