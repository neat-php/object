<?php

namespace Neat\Object\Relations;

use Neat\Object\Query;

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

    /**
     * @param $remote
     * @return array
     */
    abstract public function getRemoteKeyValue($remote);

    /**
     * @param object $local
     * @return Query
     */
    abstract public function select($local): Query;
}
