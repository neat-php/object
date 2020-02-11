<?php

namespace Neat\Object\Reference;

use Neat\Object\Reference;

interface ReferenceBuilder
{
    /**
     * Returns the created reference or calls the factory and builds the reference
     *
     * @return Reference
     */
    public function resolve(): Reference;

    /**
     * Pass a callable to modify the reference before creation, should be called before calling the resolve method
     * The signature of the callable should be compatible with:
     *  function (ReferenceBuilder $builder): void;
     * The type of the $builder should be the actually expected Builder i.e. the LocalKeyBuilder
     *
     * @param callable $factory
     * @return mixed
     */
    public function factory(callable $factory);
}
