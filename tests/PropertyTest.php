<?php

namespace Neat\Object\Test;

use DateTime;
use DateTimeImmutable;
use Neat\Object\Policy;
use Neat\Object\Property;
use Neat\Object\Test\Helper\User;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

class PropertyTest extends TestCase
{
    public function createProperty(string $name): Property
    {
        $reflection = new ReflectionProperty(User::class, $name);

        return (new Policy())->property($reflection);
    }

    /**
     * Provide type test data
     */
    public function provideTypes(): array
    {
        return [
            ['id', Property\Integer::class],
            ['username', Property::class],
            ['middleName', Property::class],
            ['lastName', Property::class],
            ['active', Property\Boolean::class],
            ['ignored', Property\Integer::class],
            ['registerDate', Property\DateTimeImmutable::class],
            ['updateDate', Property\DateTime::class],
            ['deletedDate', Property\DateTime::class],
        ];
    }

    /**
     * Test untyped property access
     *
     * @dataProvider provideTypes
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
            ['id', 1, 1],
            ['id', '1', 1],
            ['username', null, null],
            ['username', 'john', 'john'],
            ['lastName', null, null],
            ['lastName', 'Doe', 'Doe'],
            ['lastName', 3, '3'],
            ['active', null, null],
            ['active', '0', false],
            ['active', '1', true],
            ['active', 0, false],
            ['active', 1, true],
            ['ignored', null, null],
            ['ignored', '0', 0],
            ['ignored', '1', 1],
            ['ignored', 0, 0],
            ['ignored', 1, 1],
            ['registerDate', null, null],
            ['registerDate', '2001-02-03', new DateTimeImmutable('2001-02-03 00:00:00')],
            ['registerDate', '2001-02-03 04:05:06', new DateTimeImmutable('2001-02-03 04:05:06')],
            ['updateDate', null, null],
            ['updateDate', '2001-02-03', new DateTime('2001-02-03 00:00:00')],
            ['updateDate', '2001-02-03 04:05:06', new DateTime('2001-02-03 04:05:06')],
        ];
    }

    /**
     * Test set value
     *
     * @dataProvider provideSetData
     * @param string $name
     * @param mixed  $in
     * @param mixed  $out
     */
    public function testSet(string $name, $in, $out)
    {
        $user = new User();

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
            ['id', 1, '1'],
            ['id', '1', '1'],
            ['username', null, null],
            ['username', 'john', 'john'],
            ['lastName', null, null],
            ['lastName', 'Doe', 'Doe'],
            ['lastName', 3, '3'],
            ['active', null, null],
            ['active', false, '0'],
            ['active', true, '1'],
            ['ignored', null, null],
            ['ignored', '0', '0'],
            ['ignored', '1', '1'],
            ['ignored', 0, '0'],
            ['ignored', 1, '1'],
            ['registerDate', null, null],
            ['registerDate', '2001-02-03', '2001-02-03 00:00:00'],
            ['registerDate', new DateTimeImmutable('2001-02-03 04:05:06'), '2001-02-03 04:05:06'],
            ['updateDate', null, null],
            ['updateDate', '2001-02-03', '2001-02-03 00:00:00'],
            ['updateDate', new DateTime('2001-02-03 04:05:06'), '2001-02-03 04:05:06'],
        ];
    }

    /**
     * Test get value
     *
     * @dataProvider provideGetData
     * @param string $name
     * @param mixed  $in
     * @param mixed  $out
     */
    public function testGet(string $name, $in, $out)
    {
        $user        = new User();
        $user->$name = $in;

        $property = $this->createProperty($name);

        if (is_object($out)) {
            $this->assertEquals($out, $property->get($user));
        } else {
            $this->assertSame($out, $property->get($user));
        }
    }
}
