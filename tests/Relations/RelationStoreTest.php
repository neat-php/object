<?php

namespace Neat\Object\Test\Relations;

use Neat\Object\Relations\Many;
use Neat\Object\Relations\One;
use Neat\Object\Relations\Reference;
use Neat\Object\Relations\Relation;
use Neat\Object\Test\Helper\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RelationStoreTest extends TestCase
{
    /**
     * @param string $class
     * @param bool   $expect
     * @return MockObject|Reference
     */
    private function mock(string $class, bool $expect = true)
    {
        $mock = $this->createMock($class);
        if ($expect) {
            $mock->expects($this->once())->method('store')->willReturnCallback(
                function () {
                    $this->addToAssertionCount(1);
                }
            );
        } else {
            $mock->expects($this->never())->method('store')->willReturnCallback(
                function () {
                    $this->fail("Method should not be called!");
                }
            );
        }

        return $mock;
    }

    public function testStore()
    {
        $testData = [
            [new One($this->mock(Reference\LocalKey::class, false), new User()), 1],
            [new One($this->mock(Reference\RemoteKey::class), new User())],
            [new Many($this->mock(Reference\RemoteKey::class), new User())],
            [new Many($this->mock(Reference\JunctionTable::class), new User())],
        ];
        foreach ($testData as $item) {
            $this->executeStoreRelation($item[0], $item[1] ?? 0);
        }
        $this->assertSame(count($testData), $this->getNumAssertions());
    }

    private function executeStoreRelation(Relation $relation, int $assertionCount = 0)
    {
        $relation->load();
        $relation->storeRelation();
        if ($assertionCount > 0) {
            $this->addToAssertionCount($assertionCount);
        }
    }

    public function testSet()
    {
        $testData = [
            [new One($this->mock(Reference\LocalKey::class), new User())],
            [new One($this->mock(Reference\RemoteKey::class, false), new User()), 1],
            [new Many($this->mock(Reference\RemoteKey::class, false), new User()), 1],
            [new Many($this->mock(Reference\JunctionTable::class, false), new User()), 1],
        ];
        foreach ($testData as $item) {
            $this->executeSetRelation($item[0], $item[1] ?? 0);
        }
        $this->assertSame(count($testData), $this->getNumAssertions());
    }

    private function executeSetRelation(Relation $relation, int $assertionCount = 0)
    {
        $relation->load();
        $relation->setRelation();
        if ($assertionCount > 0) {
            $this->addToAssertionCount($assertionCount);
        }
    }
}
