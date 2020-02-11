<?php

namespace Neat\Object\Test\Helper;

use Neat\Object\Repository\RepositoryDecorator;
use Neat\Object\Repository;

class RepositoryDecoratorMock
{
    use RepositoryDecorator;

    /** @var Repository */
    private $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    protected function repository(): Repository
    {
        return $this->repository;
    }
}
