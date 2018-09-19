<?php

namespace Neat\Object\Relations;

abstract class Reference
{
    /**
     * @param object $local
     * @return object[]
     */
    abstract public function load($local): array;

    /**
     * @param object   $local
     * @param object[] $remotes
     * @return void
     */
    abstract public function store($local, array $remotes);
}
