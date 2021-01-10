<?php

namespace Neat\Object;

use Neat\Object\Relations\Reference\JunctionTable;
use Neat\Object\Relations\Reference\JunctionTableBuilder;
use Neat\Object\Relations\Reference\LocalKey;
use Neat\Object\Relations\Reference\LocalKeyBuilder;
use Neat\Object\Relations\Reference\RemoteKey;
use Neat\Object\Relations\Reference\RemoteKeyBuilder;

trait ReferenceFactory
{
    /** @var Cache */
    protected $references;

    /**
     * @param class-string $local
     * @param class-string $remote
     * @return RemoteKey
     */
    public function remoteKey(string $local, string $remote): RemoteKey
    {
        /** @var RemoteKey $reference */
        $reference = $this->buildRemoteKey("{$local}remote{$remote}", $local, $remote)->resolve();

        return $reference;
    }

    /**
     * @param string       $key
     * @param class-string $local
     * @param class-string $remote
     * @return RemoteKeyBuilder
     */
    public function buildRemoteKey(string $key, string $local, string $remote): RemoteKeyBuilder
    {
        /** @var RemoteKeyBuilder $builder */
        $builder = $this->references->get(
            $key,
            function () use ($local, $remote) {
                return new RemoteKeyBuilder($this->manager(), $local, $remote);
            }
        );

        return $builder;
    }

    /**
     * @param class-string $local
     * @param class-string $remote
     * @return LocalKey
     */
    public function localKey(string $local, string $remote): LocalKey
    {
        /** @var LocalKey $reference */
        $reference = $this->buildLocalKey("{$local}local{$remote}", $local, $remote)->resolve();

        return $reference;
    }

    /**
     * @param string       $key
     * @param class-string $local
     * @param class-string $remote
     * @return LocalKeyBuilder
     */
    public function buildLocalKey(string $key, string $local, string $remote): LocalKeyBuilder
    {
        /** @var LocalKeyBuilder $builder */
        $builder = $this->references->get(
            $key,
            function () use ($local, $remote) {
                return new LocalKeyBuilder($this->manager(), $local, $remote);
            }
        );

        return $builder;
    }

    /**
     * @param class-string $local
     * @param class-string $remote
     * @return JunctionTable
     */
    public function junctionTable(string $local, string $remote): JunctionTable
    {
        /** @var JunctionTable $reference */
        $reference = $this->buildJunctionTable("{$local}junctionTable{$remote}", $local, $remote)->resolve();

        return $reference;
    }

    /**
     * @param string       $key
     * @param class-string $local
     * @param class-string $remote
     * @return JunctionTableBuilder
     */
    public function buildJunctionTable(string $key, string $local, string $remote): JunctionTableBuilder
    {
        /** @var JunctionTableBuilder $builder */
        $builder = $this->references->get(
            $key,
            function () use ($local, $remote) {
                return new JunctionTableBuilder($this->manager(), $local, $remote);
            }
        );

        return $builder;
    }

    abstract public function manager(): Manager;
}
