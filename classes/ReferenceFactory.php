<?php

namespace Neat\Object;

use Neat\Database\Connection;
use Neat\Object\Relations\Reference\JunctionTable;
use Neat\Object\Relations\Reference\LocalKey;
use Neat\Object\Relations\Reference\RemoteKey;

trait ReferenceFactory
{
    /**
     * @var Cache
     */
    private $references;

    /**
     * @param string $local
     * @param string $remote
     * @return RemoteKey
     */
    public function remoteKey(string $local, string $remote): RemoteKey
    {
        /** @var RemoteKey $reference */
        $reference = $this->references->get("{$local}remote{$remote}", function () use ($local, $remote) {
            return $this->remoteKeyFactory($this->policy(), $local, $remote);
        });

        return $reference;
    }

    /**
     * @param Policy $policy
     * @param string $local
     * @param string $remote
     * @return RemoteKey
     */
    private function remoteKeyFactory(Policy $policy, string $local, string $remote): RemoteKey
    {
        $localKey         = $policy->key($local);
        $foreignKey       = $policy->foreignKey($local);
        $localProperties  = $policy->properties($local);
        $remoteProperties = $policy->properties($remote);

        return new RemoteKey(
            $localProperties[reset($localKey)],
            $remoteProperties[$foreignKey],
            $foreignKey,
            $this->repository($remote)
        );
    }

    /**
     * @param string $local
     * @param string $remote
     * @return LocalKey
     */
    public function localKey(string $local, string $remote): LocalKey
    {
        /** @var LocalKey $reference */
        $reference = $this->references->get("{$local}local{$remote}", function () use ($local, $remote) {
            return $this->localKeyFactory($this->policy(), $local, $remote);
        });

        return $reference;
    }

    /**
     * @param Policy $policy
     * @param string $local
     * @param string $remote
     * @return LocalKey
     */
    private function localKeyFactory(Policy $policy, string $local, string $remote): LocalKey
    {
        $localForeignKey  = $policy->foreignKey($remote);
        $remoteKey        = $policy->key($remote);
        $localProperties  = $policy->properties($local);
        $remoteProperties = $policy->properties($remote);

        return new LocalKey(
            $localProperties[$localForeignKey],
            $remoteProperties[reset($remoteKey)],
            reset($remoteKey),
            $this->repository($remote)
        );
    }

    public function junctionTable(string $local, string $remote): JunctionTable
    {
        /** @var JunctionTable $reference */
        $reference = $this->references->get("{$local}junctionTable{$remote}", function () use ($local, $remote) {
            return $this->junctionTableFactory($this->policy(), $local, $remote);
        });

        return $reference;
    }

    /**
     * @param Policy $policy
     * @param string $local
     * @param string $remote
     * @return JunctionTable
     */
    private function junctionTableFactory(Policy $policy, string $local, string $remote): JunctionTable
    {
        $localKey         = $policy->key($local);
        $remoteKey        = $policy->key($remote);
        $localProperties  = $policy->properties($local);
        $remoteProperties = $policy->properties($remote);
        $localForeignKey  = $policy->foreignKey($local);
        $remoteForeignKey = $policy->foreignKey($remote);

        return new JunctionTable(
            $localProperties[reset($localKey)],
            $remoteProperties[reset($remoteKey)],
            reset($remoteKey),
            $this->repository($remote),
            $this->connection(),
            $policy->junctionTable($local, $remote),
            $localForeignKey,
            $remoteForeignKey
        );
    }

    abstract public function connection(): Connection;

    abstract public function policy(): Policy;

    abstract public function repository(string $class): RepositoryInterface;
}
