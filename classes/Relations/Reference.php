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
    abstract public function load(object $local): array;

    /**
     * @param TLocal   $local
     * @param array<TRemote> $remotes
     * @return void
     */
    abstract public function store(object $local, array $remotes): void;

    /**
     * @param object $remote
     * @return array<string, mixed>
     */
    abstract public function getRemoteKeyValue(object $remote): array;

    /**
     * @param TLocal $local
     * @return Query<TRemote>
     */
    abstract public function select(object $local): Query;
}
