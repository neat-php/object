<?php

namespace Neat\Object;

use Neat\Database\Connection;
use Neat\Object\Relations\Reference\JunctionTable;
use Neat\Object\Relations\Reference\JunctionTableBuilder;
use Neat\Object\Relations\Reference\LocalKey;
use Neat\Object\Relations\Reference\LocalKeyBuilder;
use Neat\Object\Relations\Reference\RemoteKey;
use Neat\Object\Relations\Reference\RemoteKeyBuilder;

trait ReferenceFactory
{
    /**
     * @var Cache
     */
    public $references;

    /**
     * @param string $local
     * @param string $remote
     * @return RemoteKey
     */
    public function remoteKey(string $local, string $remote): RemoteKey
    {
        /** @var RemoteKey $reference */
        $reference = $this->buildRemoteKey("{$local}remote{$remote}", $local, $remote)->resolve();

        return $reference;
    }

    public function buildRemoteKey(string $key, string $local, string $remote): RemoteKeyBuilder
    {
        /** @var RemoteKeyBuilder $builder */
        $builder = $this->references->get($key, function () use ($local, $remote) {
            return new RemoteKeyBuilder($this->manager(), $this->policy(), $local, $remote);
        });

        return $builder;
    }

    /**
     * @param string $local
     * @param string $remote
     * @return LocalKey
     */
    public function localKey(string $local, string $remote): LocalKey
    {
        /** @var LocalKey $reference */
        $reference = $this->buildLocalKey("{$local}local{$remote}", $local, $remote)->resolve();

        return $reference;
    }

    /**
     * @param string $key
     * @param string $local
     * @param string $remote
     * @return LocalKeyBuilder
     */
    public function buildLocalKey(string $key, string $local, string $remote): LocalKeyBuilder
    {
        /** @var LocalKeyBuilder $builder */
        $builder = $this->references->get($key, function () use ($local, $remote) {
            return new LocalKeyBuilder($this->manager(), $this->policy(), $local, $remote);
        });

        return $builder;
    }

    public function junctionTable(string $local, string $remote): JunctionTable
    {
        /** @var JunctionTable $reference */
        $reference = $this->buildJunctionTable("{$local}junctionTable{$remote}", $local, $remote)->resolve();

        return $reference;
    }

    public function buildJunctionTable(string $key, string $local, string $remote): JunctionTableBuilder
    {
        /** @var JunctionTableBuilder $builder */
        $builder = $this->references->get($key, function () use ($local, $remote) {
            return new JunctionTableBuilder($this->manager(), $this->policy(), $local, $remote);
        });

        return $builder;
    }

    abstract public function manager(): Manager;

    abstract public function connection(): Connection;

    abstract public function policy(): Policy;

    abstract public function repository(string $class): RepositoryInterface;
}
