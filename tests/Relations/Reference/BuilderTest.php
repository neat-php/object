<?php

namespace Neat\Object\Test\Relations\Reference;

use Neat\Object\Exception\ClassMismatchException;
use Neat\Object\Exception\PropertyNotFoundException;
use Neat\Object\Policy;
use Neat\Object\Property;
use Neat\Object\Relations\Reference;
use Neat\Object\Test\Helper\Factory;
use Neat\Object\Test\Helper\ReferenceBuilderMock;
use Neat\Object\Test\Helper\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BuilderTest extends TestCase
{
    use Factory;

    public function testProperty()
    {
        /** @var ReferenceBuilderMock|MockObject $builder */
        $builder = $this->getMockForAbstractClass(ReferenceBuilderMock::class);
        /** @var Policy|MockObject $policy */
        $policy = $this->createPartialMock(Policy::class, ['properties', 'column']);
        $builder->setPolicy($policy);
        $expectedProperty = $this->createMock(Property::class);
        $property         = $this->createMock(Property::class);

        $policy
            ->expects($this->once())
            ->method('properties')
            ->with(User::class)
            ->willReturn(['id' => $expectedProperty, 'test' => $property]);
        $policy
            ->expects($this->once())
            ->method('column')
            ->with('id')
            ->willReturn('id');

        $this->assertSame($expectedProperty, $builder->property(User::class, 'id'));
    }

    public function testPropertyByObject()
    {
        /** @var ReferenceBuilderMock|MockObject $builder */
        $builder = $this->getMockForAbstractClass(ReferenceBuilderMock::class);
        /** @var Policy|MockObject $policy */
        $policy = $this->createPartialMock(Policy::class, ['properties', 'column']);
        $builder->setPolicy($policy);
        $user = new User();
        $expectedProperty = $this->createMock(Property::class);
        $property         = $this->createMock(Property::class);

        $policy
            ->expects($this->once())
            ->method('properties')
            ->with(User::class)
            ->willReturn(['id' => $expectedProperty, 'test' => $property]);
        $policy
            ->expects($this->once())
            ->method('column')
            ->with('id')
            ->willReturn('id');

        $this->assertSame($expectedProperty, $builder->property($user, 'id'));
    }

    public function testPropertyNotFound()
    {
        $this->expectException(PropertyNotFoundException::class);

        /** @var ReferenceBuilderMock|MockObject $builder */
        $builder = $this->getMockForAbstractClass(ReferenceBuilderMock::class);
        /** @var Policy|MockObject $policy */
        $policy = $this->createPartialMock(Policy::class, ['properties', 'column']);
        $builder->setPolicy($policy);

        $policy
            ->expects($this->once())
            ->method('properties')
            ->with(User::class)
            ->willReturn(['id' => $this->createMock(Property::class), 'test' => $this->createMock(Property::class)]);
        $policy
            ->expects($this->once())
            ->method('column')
            ->with('nonExistingProperty')
            ->willReturn('non_existing_property');

        $builder->property(User::class, 'nonExistingProperty');
    }

    public function testPropertyByColumn()
    {
        /** @var ReferenceBuilderMock|MockObject $builder */
        $builder = $this->getMockForAbstractClass(ReferenceBuilderMock::class);
        /** @var Policy|MockObject $policy */
        $policy = $this->createPartialMock(Policy::class, ['properties']);
        $builder->setPolicy($policy);
        $expectedProperty = $this->createMock(Property::class);
        $property         = $this->createMock(Property::class);

        $policy
            ->expects($this->once())
            ->method('properties')
            ->with(User::class)
            ->willReturn(['id' => $expectedProperty, 'test' => $property]);

        $this->assertSame($expectedProperty, $builder->propertyByColumn(User::class, 'id'));
    }

    public function testPropertyByColumnNotFound()
    {
        $this->expectException(PropertyNotFoundException::class);

        /** @var ReferenceBuilderMock|MockObject $builder */
        $builder = $this->getMockForAbstractClass(ReferenceBuilderMock::class);
        /** @var Policy|MockObject $policy */
        $policy = $this->createPartialMock(Policy::class, ['properties']);
        $builder->setPolicy($policy);

        $policy
            ->expects($this->once())
            ->method('properties')
            ->with(User::class)
            ->willReturn(['id' => $this->createMock(Property::class), 'test' => $this->createMock(Property::class)]);

        $builder->propertyByColumn(User::class, 'non_existing_column');
    }

    public function testResolve()
    {
        $resolved = $this->getMockForAbstractClass(Reference::class);
        /** @var ReferenceBuilderMock|MockObject $builder */
        $builder = $this->getMockForAbstractClass(ReferenceBuilderMock::class);
        $builder->setClass(MockObject::class);
        $builder->expects($this->once())->method('build')->willReturn($resolved);

        $this->assertSame($resolved, $builder->resolve());
    }

    public function testResolveException()
    {
        $resolved = $this->createMock(Reference\RemoteKey::class);
        /** @var ReferenceBuilderMock|MockObject $builder */
        $builder = $this->getMockForAbstractClass(ReferenceBuilderMock::class);
        $builder->setClass(Reference\LocalKey::class);
        $builder->expects($this->once())->method('build')->willReturn($resolved);

        $this->expectExceptionObject(new ClassMismatchException(Reference\LocalKey::class, get_class($resolved)));
        $builder->resolve();
    }
}
