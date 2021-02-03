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
     * @param string        $key
     * @param class-string  $local
     * @param class-string  $remote
     * @param callable|null $configure
     * @psalm-param callable(RemoteKeyBuilder)|null $configure
     * @return RemoteKey
     */
    public function remoteKey(string $key, string $local, string $remote, callable $configure = null): RemoteKey
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
     * @param string        $key
     * @param class-string  $local
     * @param class-string  $remote
     * @param callable|null $configure
     * @psalm-param callable(LocalKeyBuilder)|null $configure
     * @return LocalKey
     */
    public function localKey(string $key, string $local, string $remote, callable $configure = null): LocalKey
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
     * @param string        $key
     * @param class-string  $local
     * @param class-string  $remote
     * @param callable|null $configure
     * @psalm-param callable(JunctionTableBuilder)|null $configure
     * @return JunctionTable
     */
    public function junctionTable(string $key, string $local, string $remote, callable $configure = null): JunctionTable
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
