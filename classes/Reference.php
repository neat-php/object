<?php

namespace Neat\Object;

interface Reference
{
    /**
     * @param object $local
     * @return object[]
     */
    public function load($local): array;

    /**
     * @param object   $local
     * @param object[] $remotes
     * @return void
     */
    public function store($local, array $remotes);

    /**
     * @param $remote
     * @return array
     */
    public function getRemoteKeyValue($remote);

    /**
     * @param object $local
     * @return Query
     */
    public function select($local): Query;
}
