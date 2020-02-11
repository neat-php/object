<?php

namespace Neat\Object\Test\Relations\Reference;

use Neat\Object\Reference\JunctionTable;
use Neat\Object\Reference\JunctionTableBuilder;
use Neat\Object\Test\Helper\Address;
use Neat\Object\Test\Helper\Factory;
use Neat\Object\Test\Helper\User;
use PHPUnit\Framework\TestCase;

class JunctionTableBuilderTest extends TestCase
{
    use Factory;

    private function junctionTableBuilder()
    {
        return new JunctionTableBuilder($this->manager(), User::class, Address::class);
    }

    public function testBuild()
    {
        $repository = $this->repository(Address::class);
        $builder    = $this->junctionTableBuilder();
        $localKey   = $builder->property(User::class, 'typeId');
        $this->assertSame($builder, $builder->setLocalKey($localKey));
        $remoteKey = $builder->property(Address::class, 'street');
        $this->assertSame($builder, $builder->setRemoteKey($remoteKey));
        $this->assertSame($builder, $builder->setRemoteKeyColumn('test_remote_key_column'));
        $this->assertSame($builder, $builder->setRemoteRepository($repository));
        $this->assertSame($builder, $builder->setJunctionTable('test_junction_table'));
        $this->assertSame($builder, $builder->setJunctionTableLocalForeignKey('test_local_foreign_key'));
        $this->assertSame($builder, $builder->setJunctionTableRemoteForeignKey('test_remote_foreign_key'));

        $this->assertEquals(
            new JunctionTable(
                $localKey,
                $remoteKey,
                'test_remote_key_column',
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
