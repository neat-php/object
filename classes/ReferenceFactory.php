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
    public function remoteKey(string $key, string $local, string $remote, ?callable $configure = null): RemoteKey
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->references->get($key, function () use ($local, $remote, $configure) {
            $builder = new RemoteKeyBuilder($this->manager(), $local, $remote);
            if ($configure) {
                $configure($builder);
            }

            return $builder->resolve();
        });
    }

    /**
     * @template TLocal
     * @template TRemote
     * @param string $key
     * @param class-string<TLocal>                 $local
     * @param class-string<TRemote>                $remote
     * @param null|callable(LocalKeyBuilder): void $configure
     * @return LocalKey<TLocal, TRemote>
     */
    public function localKey(string $key, string $local, string $remote, ?callable $configure = null): LocalKey
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->references->get($key, function () use ($local, $remote, $configure) {
            $builder = new LocalKeyBuilder($this->manager(), $local, $remote);
            if ($configure) {
                $configure($builder);
            }

            return $builder->resolve();
        });
    }

    /**
     * @template TLocal
     * @template TRemote
     * @param string $key
     * @param class-string<TLocal>                                       $local
     * @param class-string<TRemote>                                      $remote
     * @param null|callable(JunctionTableBuilder<TLocal, TRemote>): void $configure
     * @return JunctionTable<TLocal, TRemote>
     */
    public function junctionTable(string $key, string $local, string $remote, ?callable $configure = null): JunctionTable
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->references->get($key, function () use ($local, $remote, $configure) {
            $builder = new JunctionTableBuilder($this->manager(), $local, $remote);
            if ($configure) {
                $configure($builder);
            }

            return $builder->resolve();
        });
    }

    abstract public function manager(): Manager;
}
