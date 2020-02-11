<?php

namespace Neat\Object\Test\Relations;

use Neat\Object\Relations\Many;
use Neat\Object\Relations\One;
use Neat\Object\Relations\Reference\LocalKey;
use Neat\Object\Relations\ReferenceBuilder;
use Neat\Object\Relations\RelationBuilder;
use Neat\Object\Test\Helper\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RelationBuilderTest extends TestCase
{
    /**
     * @return ReferenceBuilder|MockObject
     */
    private function reference(): ReferenceBuilder
    {
        return $this->getMockForAbstractClass(ReferenceBuilder::class);
    }

    public function testReferenceFactory()
    {
        $callable         = function () {
        };
        $referenceBuilder = $this->reference();
        $referenceBuilder->expects($this->once())->method('factory')->with($callable);
        $relation = new RelationBuilder(One::class, $referenceBuilder, new User());
        $relation->referenceFactory($callable);
    }

    public function provideResolveData()
    {
        return [
            [One::class],
            [Many::class],
        ];
    }

    /**
     * @dataProvider provideResolveData
     * @param string $class
     */
    public function testResolve(string $class)
    {
        $referenceBuilder = $this->reference();
        /** @var LocalKey|MockObject $localKey */
        $localKey = $this->getMockBuilder(LocalKey::class)->disableOriginalConstructor()->getMock();
        $referenceBuilder->expects($this->once())->method('resolve')->willReturn($localKey);
        $user     = new User();
        $relation = new RelationBuilder($class, $referenceBuilder, $user);
        $expected = new $class($localKey, $user);
        $this->assertEquals($expected, $relation->resolve());
    }
}
