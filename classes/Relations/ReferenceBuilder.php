<?php

namespace Neat\Object\Relations;

interface ReferenceBuilder
{
    /**
     * Returns the created reference or calls the factory and builds the reference
     *
     * @return Reference
     */
    public function resolve(): Reference;
}
