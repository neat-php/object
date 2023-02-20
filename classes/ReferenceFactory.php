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
     * @template TLocal
     * @template TRemote
     * @param class-string<TLocal>                                   $local
     * @param class-string<TRemote>                                  $remote
     * @param null|callable(RemoteKeyBuilder<TLocal, TRemote>): void $configure
     * @return RemoteKey<TLocal, TRemote>
     */
    public function remoteKey(string $local, string $remote, callable $configure = null): RemoteKey
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        /** @noinspection PhpDeprecationInspection */
        return $this->buildRemoteKey("{$local}remote{$remote}", $local, $remote, $configure)->resolve();
    }

    /**
     * @template   TLocal
     * @template   TRemote
     * @param string                                                 $key
     * @param class-string<TLocal>                                   $local
     * @param class-string<TRemote>                                  $remote
     * @param null|callable(RemoteKeyBuilder<TLocal, TRemote>): void $configure
     * @return RemoteKeyBuilder<TLocal, TRemote>
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
     * @template TLocal
     * @template TRemote
     * @param class-string<TLocal>                 $local
     * @param class-string<TRemote>                $remote
     * @param null|callable(LocalKeyBuilder): void $configure
     * @return LocalKey<TLocal, TRemote>
     */
    public function localKey(string $local, string $remote, callable $configure = null): LocalKey
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        /** @noinspection PhpDeprecationInspection */
        return $this->buildLocalKey("{$local}local{$remote}", $local, $remote, $configure)->resolve();
    }

    /**
     * @template   TLocal
     * @template   TRemote
     * @param string                                                $key
     * @param class-string<TLocal>                                  $local
     * @param class-string<TRemote>                                 $remote
     * @param null|callable(LocalKeyBuilder<TLocal, TRemote>): void $configure
     * @return LocalKeyBuilder<TLocal, TRemote>
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
     * @template TLocal
     * @template TRemote
     * @param class-string<TLocal>                                       $local
     * @param class-string<TRemote>                                      $remote
     * @param null|callable(JunctionTableBuilder<TLocal, TRemote>): void $configure
     * @return JunctionTable<TLocal, TRemote>
     */
    public function junctionTable(string $local, string $remote, callable $configure = null): JunctionTable
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        /** @noinspection PhpDeprecationInspection */
        return $this->buildJunctionTable("{$local}junctionTable{$remote}", $local, $remote, $configure)->resolve();
    }

    /**
     * @template   TLocal
     * @template   TRemote
     * @param string                                                     $key
     * @param class-string<TLocal>                                       $local
     * @param class-string<TRemote>                                      $remote
     * @param null|callable(JunctionTableBuilder<TLocal, TRemote>): void $configure
     * @return JunctionTableBuilder<TLocal, TRemote>
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
