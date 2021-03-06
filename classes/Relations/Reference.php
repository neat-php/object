<?php

namespace Neat\Object\Relations;

use Neat\Object\Query;

abstract class Reference
{
    /**
     * @param object $local
     * @return object[]
     */
    abstract public function load(object $local): array;

    /**
     * @param object   $local
     * @param object[] $remotes
     * @return void
     */
    abstract public function store(object $local, array $remotes): void;

    /**
     * @param object $remote
     * @return array
     */
    abstract public function getRemoteKeyValue(object $remote): array;

    /**
     * @param object $local
     * @return Query
     */
    abstract public function select(object $local): Query;
}
