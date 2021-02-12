<?php

namespace Neat\Object\Test\Relations\Reference;

use Neat\Object\Relations\Reference\JunctionTable;
use Neat\Object\Relations\Reference\JunctionTableBuilder;
use Neat\Object\Test\Helper\Address;
use Neat\Object\Test\Helper\Factory;
use Neat\Object\Test\Helper\User;
use PHPUnit\Framework\TestCase;

class JunctionTableBuilderTest extends TestCase
{
    use Factory;

    private function junctionTableBuilder(): JunctionTableBuilder
    {
        return new JunctionTableBuilder($this->manager(), User::class, Address::class);
    }

    public function testBuild(): void
    {
        $repository = $this->repository(Address::class);
        $builder    = $this->junctionTableBuilder();
        $this->assertSame($builder, $builder->setLocalKey('typeId'));
        $this->assertSame($builder, $builder->setRemoteKey('street'));
        $this->assertSame($builder, $builder->setRemoteRepository($repository));
        $this->assertSame($builder, $builder->setJunctionTable('test_junction_table'));
        $this->assertSame($builder, $builder->setJunctionTableLocalKeyColumn('test_local_foreign_key'));
        $this->assertSame($builder, $builder->setJunctionTableRemoteKeyColumn('test_remote_foreign_key'));

        $this->assertEquals(
            new JunctionTable(
                $builder->property(User::class, 'typeId'),
                $builder->property(Address::class, 'street'),
                'street',
                $repository,
                $this->connection(),
                'test_junction_table',
                'test_local_foreign_key',
                'test_remote_foreign_key'
            ),
            $builder->resolve()
        );
    }

    public function testBuildColumn(): void
    {
        $repository = $this->repository(Address::class);
        $builder    = $this->junctionTableBuilder();
        $this->assertSame($builder, $builder->setLocalKeyColumn('type_id'));
        $this->assertSame($builder, $builder->setRemoteKeyColumn('street'));
        $this->assertSame($builder, $builder->setRemoteRepository($repository));
        $this->assertSame($builder, $builder->setJunctionTable('test_junction_table'));
        $this->assertSame($builder, $builder->setJunctionTableLocalKeyColumn('test_local_foreign_key'));
        $this->assertSame($builder, $builder->setJunctionTableRemoteKeyColumn('test_remote_foreign_key'));

        $this->assertEquals(
            new JunctionTable(
                $builder->propertyByColumn(User::class, 'type_id'),
                $builder->propertyByColumn(Address::class, 'street'),
                'street',
                $repository,
                $this->connection(),
                'test_junction_table',
                'test_local_foreign_key',
                'test_remote_foreign_key'
            ),
            $builder->resolve()
        );
    }
}
