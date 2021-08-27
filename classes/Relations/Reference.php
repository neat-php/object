<?php

namespace Neat\Object\Relations;

use Neat\Object\Query;

/**
 * @template TLocal of object
 * @template TRemote of object
 */
abstract class Reference
{
    /**
     * @param TLocal $local
     * @return array<TRemote>
     */
    abstract public function load($local): array;

    /**
     * @param TLocal   $local
     * @param array<TRemote> $remotes
     * @return void
     */
    abstract public function store($local, array $remotes);

    /**
     * @param $remote
     * @return array<string, mixed>
     */
    abstract public function getRemoteKeyValue($remote);

    /**
     * @param TLocal $local
     * @return Query<TRemote>
     */
    abstract public function select($local): Query;
}
