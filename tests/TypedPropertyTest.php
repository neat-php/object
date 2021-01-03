<?php

namespace Neat\Object\Test;

use Neat\Object\Policy;
use Neat\Object\Property;
use Neat\Object\Test\Helper\Contact;
use Neat\Object\Test\Helper\Phone;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

/**
 * @requires     PHP >= 7.4
 */
class TypedPropertyTest extends TestCase
{
    public function createProperty(string $name): Property
    {
        $reflection = new ReflectionProperty(Contact::class, $name);

        return (new Policy())->property($reflection);
    }

    /**
     * Provide type test data
     */
    public function provideTypes(): array
    {
        return [
            ['id', Property\Integer::class],
            ['email', Property::class],
            ['phone', Property\Serializable::class],
            ['data', Property::class],
            ['default', Property\Boolean::class],
            ['createdAt', Property\DateTime::class],
            ['deletedAt', Property\DateTimeImmutable::class],
        ];
    }

    /**
     * Test untyped property access
     *
     * @dataProvider provideTypes
     *
     * @param string $name
     * @param mixed  $type
     */
    public function testType(string $name, $type)
    {
        $property = $this->createProperty($name);

        $this->assertSame($name, $property->name());
        if ($type !== null) {
            $this->assertInstanceOf($type, $property);
        }
    }

    /**
     * Provide set values
     *
     * @return array
     */
    public function provideSetData(): array
    {
        return [
            ['phone', '31612345678', new Phone(31612345678)],
            ['phone', null, null],
        ];
    }

    /**
     * Test set value
     *
     * @dataProvider provideSetData
     *
     * @param string $name
     * @param mixed  $in
     * @param mixed  $out
     */
    public function testSet(string $name, $in, $out)
    {
        $user = new Contact();

        $property = $this->createProperty($name);
        $property->set($user, $in);

        if (is_object($out)) {
            $this->assertEquals($out, $user->$name);
        } else {
            $this->assertSame($out, $user->$name);
        }
    }

    /**
     * Provide get values
     *
     * @return array
     */
    public function provideGetData(): array
    {
        return [
            ['phone', new Phone(31612345678), '31612345678'],
            ['phone', null, null],
        ];
    }

    /**
     * Test get value
     *
     * @dataProvider provideGetData
     *
     * @param string $name
     * @param mixed  $in
     * @param mixed  $out
     */
    public function testGet(string $name, $in, $out)
    {
        $user        = new Contact();
        $user->$name = $in;

        $property = $this->createProperty($name);

        if (is_object($out)) {
            $this->assertEquals($out, $property->get($user));
        } else {
            $this->assertSame($out, $property->get($user));
        }
    }
}
