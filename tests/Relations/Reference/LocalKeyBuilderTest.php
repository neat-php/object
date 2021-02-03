<?php

namespace Neat\Object\Test\Relations\Reference;

use Neat\Object\Relations\Reference\LocalKey;
use Neat\Object\Relations\Reference\LocalKeyBuilder;
use Neat\Object\Test\Helper\Address;
use Neat\Object\Test\Helper\Factory;
use Neat\Object\Test\Helper\User;
use PHPUnit\Framework\TestCase;

class LocalKeyBuilderTest extends TestCase
{
    use Factory;

    private function localKeyBuilder(): LocalKeyBuilder
    {
        return new LocalKeyBuilder($this->manager(), Address::class, User::class);
    }

    public function testBuild()
    {
        $repository = $this->repository(Address::class);
        $builder    = $this->localKeyBuilder();
        $this->assertSame($builder, $builder->setLocalKey('street'));
        $this->assertSame($builder, $builder->setRemoteKey('typeId'));
        $this->assertSame($builder, $builder->setRemoteRepository($repository));

        $this->assertEquals(
            new LocalKey(
                $builder->property(Address::class, 'street'),
                $builder->property(User::class, 'typeId'),
                'type_id',
                $repository
            ),
            $builder->resolve()
        );
    }

    public function testBuildColumn()
    {
        $repository = $this->repository(Address::class);
        $builder    = $this->localKeyBuilder();
        $this->assertSame($builder, $builder->setLocalKeyColumn('street'));
        $this->assertSame($builder, $builder->setRemoteKeyColumn('type_id'));
        $this->assertSame($builder, $builder->setRemoteRepository($repository));

        $this->assertEquals(
            new LocalKey(
                $builder->propertyByColumn(Address::class, 'street'),
                $builder->propertyByColumn(User::class, 'type_id'),
                'type_id',
                $repository
            ),
            $builder->resolve()
        );
    }
}
