<?php

namespace Neat\Object\Repository;

use Neat\Object\Property;
use Neat\Object\Repository;

abstract class TimeStamp implements Repository
{
    use RepositoryDecorator;

    /** @var Repository */
    protected $repository;

    /** @var string */
    protected $column;

    /** @var Property */
    protected $property;

    /**
     * SoftDelete constructor.
     *
     * @param Repository $repository
     * @param string     $column
     * @param Property   $property
     */
    public function __construct(Repository $repository, string $column, Property $property)
    {
        $this->repository = $repository;
        $this->column     = $column;
        $this->property   = $property;
    }

    /**
     * @return Repository
     */
    protected function repository(): Repository
    {
        return $this->repository;
    }
}
