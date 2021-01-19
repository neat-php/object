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
     * @param class-string  $local
     * @param class-string  $remote
     * @param callable|null $configure
     * @psalm-param callable(RemoteKeyBuilder)|null $configure
     * @return RemoteKey
     */
    public function remoteKey(string $local, string $remote, callable $configure = null): RemoteKey
    {
        /** @var RemoteKey $reference */
        $reference = $this->buildRemoteKey("{$local}remote{$remote}", $local, $remote, $configure)->resolve();

        return $reference;
    }

    /**
     * @param string        $key
     * @param class-string  $local
     * @param class-string  $remote
     * @param callable|null $configure
     * @psalm-param callable(RemoteKeyBuilder)|null $configure
     * @return RemoteKeyBuilder
     * @deprecated Use remoteKey() instead
     */
    public function buildRemoteKey(string $key, string $local, string $remote, callable $configure = null): RemoteKeyBuilder
    {
        /** @var RemoteKeyBuilder $builder */
        $builder = $this->references->get(
            $key,
            function () use ($local, $remote, $configure) {
                $builder = new RemoteKeyBuilder($this->manager(), $local, $remote);
                if ($configure) {
                    $configure($builder);
                }

                return $builder;
            }
        );

        return $builder;
    }

    /**
     * @param class-string  $local
     * @param class-string  $remote
     * @param callable|null $configure
     * @psalm-param callable(LocalKeyBuilder)|null $configure
     * @return LocalKey
     */
    public function localKey(string $local, string $remote, callable $configure = null): LocalKey
    {
        /** @var LocalKey $reference */
        $reference = $this->buildLocalKey("{$local}local{$remote}", $local, $remote, $configure)->resolve();

        return $reference;
    }

    /**
     * @param string        $key
     * @param class-string  $local
     * @param class-string  $remote
     * @param callable|null $configure
     * @psalm-param callable(LocalKeyBuilder)|null $configure
     * @return LocalKeyBuilder
     * @deprecated Use localKey() instead
     */
    public function buildLocalKey(string $key, string $local, string $remote, callable $configure = null): LocalKeyBuilder
    {
        /** @var LocalKeyBuilder $builder */
        $builder = $this->references->get(
            $key,
            function () use ($local, $remote, $configure) {
                $builder = new LocalKeyBuilder($this->manager(), $local, $remote);
                if ($configure) {
                    $configure($builder);
                }

                return $builder;
            }
        );

        return $builder;
    }

    /**
     * @param class-string  $local
     * @param class-string  $remote
     * @param callable|null $configure
     * @psalm-param callable(JunctionTableBuilder)|null $configure
     * @return JunctionTable
     */
    public function junctionTable(string $local, string $remote, callable $configure = null): JunctionTable
    {
        /** @var JunctionTable $reference */
        $reference = $this->buildJunctionTable("{$local}junctionTable{$remote}", $local, $remote,
            $configure)->resolve();

        return $reference;
    }

    /**
     * @param string        $key
     * @param class-string  $local
     * @param class-string  $remote
     * @param callable|null $configure
     * @psalm-param callable(JunctionTableBuilder)|null $configure
     * @return JunctionTableBuilder
     * @deprecated Use junctionTable() instead
     */
    public function buildJunctionTable(string $key, string $local, string $remote, callable $configure = null): JunctionTableBuilder
    {
        /** @var JunctionTableBuilder $builder */
        $builder = $this->references->get(
            $key,
            function () use ($local, $remote, $configure) {
                $builder = new JunctionTableBuilder($this->manager(), $local, $remote);
                if ($configure) {
                    $configure($builder);
                }

                return $builder;
            }
        );

        return $builder;
    }

    abstract public function manager(): Manager;
}
