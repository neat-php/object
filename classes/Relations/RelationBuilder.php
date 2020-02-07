<?php

namespace Neat\Object\Relations;

class RelationBuilder
{
    /** @var Relation|null */
    private $resolved;

    /** @var string */
    private $class;

    /** @var ReferenceBuilder */
    private $builder;

    /** @var object */
    private $local;

    /**
     * RelationBuilder constructor.
     * @param string           $class
     * @param ReferenceBuilder $builder
     * @param object           $local
     */
    public function __construct(string $class, ReferenceBuilder $builder, $local)
    {
        $this->class   = $class;
        $this->builder = $builder;
        $this->local   = $local;
    }

    /**
     * @return Relation
     */
    public function resolve(): Relation
    {
        if ($this->resolved === null) {
            $this->build();
        }

        return $this->resolved;
    }

    /**
     * @return void
     */
    private function build()
    {
        $this->resolved = new $this->class($this->builder->resolve(), $this->local);
    }

    /**
     * @param callable $factory
     * @return $this
     */
    public function referenceFactory(callable $factory): self
    {
        $this->builder->factory($factory);

        return $this;
    }
}
