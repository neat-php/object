<?php

namespace Neat\Object\Test;

use Neat\Object\Manager;
use Neat\Object\Policy;
use Neat\Object\Query;
use Neat\Object\Relations\Many;
use Neat\Object\Relations\One;
use PHPUnit\Framework\TestCase;

class AccessorsTest extends TestCase
{
    use Helper\Factory;

    public function policy(): Policy
    {
        return new Policy(null, function (string $singular) {
            return $singular . 's';
        });
    }

    public function provideAccessorCalls(): array
    {
        return [
            ['getType', [], new Helper\Type(), One::class, 'type', 'get'],
            ['gettype', [], new Helper\Type(), One::class, 'type', 'get'],
            ['setType', [new Helper\Type()], null, One::class, 'type', 'set'],
            ['SetType', [new Helper\Type()], null, One::class, 'type', 'set'],
            ['getGroups', [], [new Helper\Group(), new Helper\Group()], Many::class, 'groups', 'get'],
            ['allGroups', [], [new Helper\Group(), new Helper\Group()], Many::class, 'groups', 'all'],
            ['setGroups', [[new Helper\Group(), new Helper\Group()]], null, Many::class, 'groups', 'set'],
            ['setGroups', [[]], null, Many::class, 'groups', 'set'],
            ['hasGroup', [new Helper\Group()], true, Many::class, 'groups', 'has'],
            ['addGroup', [new Helper\Group()], null, Many::class, 'groups', 'add'],
            ['removeGroup', [new Helper\Group()], null, Many::class, 'groups', 'remove'],
            ['selectGroups', [], $this->createMock(Query::class), Many::class, 'groups', 'select'],
        ];
    }

    /**
     * @dataProvider provideAccessorCalls
     * @param string $method
     * @param array  $arguments
     * @param mixed  $result
     * @param string $type
     * @param string $relation
     * @param string $operation
     */
    public function testAccessor(string $method, array $arguments, $result, string $type, string $relation, string $operation): void
    {
        Manager::set($this->manager());

        $relationMock = $this->createMock($type);
        $relationMock->expects($this->once())->method($operation)->with(...$arguments)->willReturn($result ?? $relationMock);

        $entityMock = $this->createPartialMock(Helper\UserWithAccessors::class, [$relation]);
        $entityMock->expects($this->once())->method($relation)->with()->willReturn($relationMock);

        $this->assertEquals($result ?? $relationMock, $entityMock->$method(...$arguments));
        Manager::unset();
    }

    public function provideExceptionCalls(): array
    {
        return [
            ['unsetProperties'],
            ['getWhatever'],
            ['doStuff'],
            ['x'],
        ];
    }

    /**
     * @dataProvider provideExceptionCalls
     * @param string $method
     */
    public function testException(string $method): void
    {
        try {
            Manager::set($this->manager());
            $this->expectException('Error');

            $user = new Helper\UserWithAccessors();
            $user->$method();
        } finally {
            Manager::unset();
        }
    }
}
