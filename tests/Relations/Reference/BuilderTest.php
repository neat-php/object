<?php

namespace Neat\Object\Test\Relations\Reference;

use Neat\Object\Exception\ClassMismatchException;
use Neat\Object\Exception\ClassNotFoundException;
use Neat\Object\Exception\PropertyNotFoundException;
use Neat\Object\Policy;
use Neat\Object\Property;
use Neat\Object\Relations\Reference;
use Neat\Object\Test\Helper\CallableMock;
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
        $policy = $this->getMockBuilder(Policy::class)->setMethods(['properties', 'column'])->getMock();
        $builder->setPolicy($policy);
        $expectedProperty = $this->getMockBuilder(Property::class)->disableOriginalConstructor()->getMock();
        $property         = $this->getMockBuilder(Property::class)->disableOriginalConstructor()->getMock();
        $policy->expects($this->once())->method('properties')
            ->with(User::class)
            ->willReturn(['id' => $expectedProperty, 'test' => $property]);
        $policy->expects($this->once())->method('column')->with('id')->willReturn('id');
        $this->assertSame($expectedProperty, $builder->property(User::class, 'id'));
    }

    public function testPropertyOnObject()
    {
        /** @var ReferenceBuilderMock|MockObject $builder */
        $builder = $this->getMockForAbstractClass(ReferenceBuilderMock::class);
        /** @var Policy|MockObject $policy */
        $policy = $this->getMockBuilder(Policy::class)->setMethods(['properties', 'column'])->getMock();
        $builder->setPolicy($policy);
        $expectedProperty = $this->getMockBuilder(Property::class)->disableOriginalConstructor()->getMock();
        $property         = $this->getMockBuilder(Property::class)->disableOriginalConstructor()->getMock();
        $policy->expects($this->once())->method('properties')
            ->with(User::class)
            ->willReturn(['id' => $expectedProperty, 'test' => $property]);
        $policy->expects($this->once())->method('column')->with('id')->willReturn('id');
        $this->assertSame($expectedProperty, $builder->property(new User(), 'id'));
    }

    public function testPropertyClassNotFound()
    {
        /** @var ReferenceBuilderMock|MockObject $builder */
        $builder = $this->getMockForAbstractClass(ReferenceBuilderMock::class);
        $class   = 'ThisIsANonExistingClass';
        $this->expectExceptionObject(new ClassNotFoundException($class));
        $builder->property($class, 'a');
    }

    public function testPropertyNonExistingProperty()
    {
        /** @var ReferenceBuilderMock|MockObject $builder */
        $builder  = $this->getMockForAbstractClass(ReferenceBuilderMock::class);
        $property = 'abcd';
        $this->expectExceptionObject(new PropertyNotFoundException(User::class, $property));
        $builder->property(User::class, $property);
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

    public function testResolveCached()
    {
        /** @var ReferenceBuilderMock|MockObject $builder */
        $builder  = $this->getMockForAbstractClass(ReferenceBuilderMock::class);
        $resolved = $this->getMockForAbstractClass(Reference::class);
        $builder->setResolved($resolved);
        $this->assertSame($resolved, $builder->resolve());
    }

    public function testResolveFactory()
    {
        $resolved = $this->getMockForAbstractClass(Reference::class);
        /** @var ReferenceBuilderMock|MockObject $builder */
        $builder = $this->getMockForAbstractClass(ReferenceBuilderMock::class);
        $builder->setClass(MockObject::class);
        $builder->expects($this->once())->method('build')->willReturn($resolved);
        /** @var CallableMock|MockObject $callable */
        $callable = $this->getMockBuilder(CallableMock::class)->setMethods(['__invoke'])->getMock();
        $callable->expects($this->once())->method('__invoke')->willReturn($builder);

        $builder->factory($callable);
        $this->assertSame($resolved, $builder->resolve());
    }

    public function testResolveException()
    {
        $resolved = $this->getMockBuilder(Reference\RemoteKey::class)
            ->disableOriginalConstructor()
            ->setMethods(['build'])
            ->getMock();
        /** @var ReferenceBuilderMock|MockObject $builder */
        $builder = $this->getMockForAbstractClass(ReferenceBuilderMock::class);
        $builder->setClass(Reference\LocalKey::class);
        $builder->expects($this->once())->method('build')->willReturn($resolved);

        $this->expectExceptionObject(new ClassMismatchException(Reference\LocalKey::class, get_class($resolved)));
        $builder->resolve();
    }
}
