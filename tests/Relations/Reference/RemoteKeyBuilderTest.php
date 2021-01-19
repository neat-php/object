<?php

namespace Neat\Object\Test\Relations\Reference;

use Neat\Object\Relations\Reference\RemoteKey;
use Neat\Object\Relations\Reference\RemoteKeyBuilder;
use Neat\Object\Test\Helper\Address;
use Neat\Object\Test\Helper\Factory;
use Neat\Object\Test\Helper\User;
use PHPUnit\Framework\TestCase;

class RemoteKeyBuilderTest extends TestCase
{
    use Factory;

    private function remoteKeyBuilder(): RemoteKeyBuilder
    {
        return new RemoteKeyBuilder($this->manager(), User::class, Address::class);
    }

    public function testBuild()
    {
        $repository = $this->repository(User::class);
        $builder    = $this->remoteKeyBuilder();
        $this->assertSame($builder, $builder->setLocalKey('typeId'));
        $this->assertSame($builder, $builder->setRemoteKey('street'));
        $this->assertSame($builder, $builder->setRemoteRepository($repository));

        $this->assertEquals(
            new RemoteKey(
                $builder->property(User::class, 'typeId'),
                $builder->property(Address::class, 'street'),
                'street',
                $repository
            ),
            $builder->resolve()
        );
    }

    public function testBuildColumn()
    {
        $repository = $this->repository(User::class);
        $builder    = $this->remoteKeyBuilder();
        $this->assertSame($builder, $builder->setLocalKeyColumn('type_id'));
        $this->assertSame($builder, $builder->setRemoteKeyColumn('street'));
        $this->assertSame($builder, $builder->setRemoteRepository($repository));

        $this->assertEquals(
            new RemoteKey(
                $builder->propertyByColumn(User::class, 'type_id'),
                $builder->propertyByColumn(Address::class, 'street'),
                'street',
                $repository
            ),
            $builder->resolve()
        );
    }

    public function testBuildProperty()
    {
        $repository = $this->repository(User::class);
        $builder    = $this->remoteKeyBuilder();
        $localKey   = $builder->property(Address::class, 'street');
        $this->assertSame($builder, $builder->setLocalKey($localKey));
        $remoteKey = $builder->property(User::class, 'typeId');
        $this->assertSame($builder, $builder->setRemoteKey($remoteKey));
        $this->assertSame($builder, $builder->setRemoteRepository($repository));

        $this->assertEquals(
            new RemoteKey(
                $localKey,
                $remoteKey,
                'type_id',
                $repository
            ),
            $builder->resolve()
        );
    }
}
