<?php

namespace Neat\Object\Test;

use DateTime;
use Neat\Object\Manager;
use Neat\Object\Test\Helper\Address;
use Neat\Object\Test\Helper\Factory;
use Neat\Object\Test\Helper\Group;
use Neat\Object\Test\Helper\Property;
use Neat\Object\Test\Helper\Type;
use Neat\Object\Test\Helper\User;
use PHPUnit\Framework\TestCase;

class RepositoryRelationsTest extends TestCase
{
    use Factory;

    /**
     * @runInSeparateProcess
     */
    public function testStore()
    {
        Manager::set($this->manager());
        $user               = new User();
        $user->username     = 'repository-relations-test';
        $user->firstName    = 'repository';
        $user->lastName     = 'relations';
        $user->active       = true;
        $user->registerDate = new DateTime('2020-04-21');
        $user->updateDate   = new DateTime('2020-04-21');

        $address       = new Address();
        $address->city = 'test';
        $user->address()->set($address);

        $property        = new Property();
        $property->name  = 'test-name1';
        $property->value = 'test-value1';
        $user->properties()->add($property);
        $property1        = new Property();
        $property1->name  = 'test-name2';
        $property1->value = 'test-value2';
        $user->properties()->add($property1);

        $type     = new Type();
        $type->id = 1;
        $user->type()->set($type);

        $user->groups()->set([Group::get(1), Group::get(2)]);
        $user->store();
        $this->assert($user->id);
    }

    private function assert(int $userId)
    {
        $user = User::get($userId);
        $this->assertNotNull($user);
        /** @var Address $address */
        $address = $user->address()->get();
        /** @var Property[] $properties */
        $properties = $user->properties()->all();
        /** @var Type $type */
        $type = $user->type()->get();
        /** @var Group[] $groups */
        $groups = $user->groups()->all();

        $this->assertSame('test', $address->city);
        $this->assertCount(2, $properties);
        $this->assertSame(1, $type->id);
        $this->assertCount(2, $groups);
    }
}
