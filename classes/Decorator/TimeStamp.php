<?php

namespace Neat\Object\Decorator;

use Neat\Object\Property;
use Neat\Object\RepositoryDecorator;
use Neat\Object\RepositoryInterface;

abstract class TimeStamp implements RepositoryInterface
{
    use RepositoryDecorator;

    /** @var RepositoryInterface */
    protected $repository;

    /** @var string */
    protected $column;

    /** @var Property */
    protected $property;

    /**
     * SoftDelete constructor.
     * @param RepositoryInterface $repository
     * @param string              $column
     * @param Property            $property
     */
    public function __construct(RepositoryInterface $repository, string $column, Property $property)
    {
        $this->repository = $repository;
        $this->column     = $column;
        $this->property   = $property;
    }

    /**
     * @return RepositoryInterface
     */
    protected function repository(): RepositoryInterface
    {
        return $this->repository;
    }
}
