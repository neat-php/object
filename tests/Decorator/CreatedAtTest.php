<?php

namespace Neat\Object\Test\Decorator;

use DateTime;
use Neat\Object\Repository\CreatedAt;
use Neat\Object\Repository\Repository;
use Neat\Object\RepositoryInterface;
use Neat\Object\Test\Helper\Factory;
use Neat\Object\Test\Helper\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CreatedAtTest extends TestCase
{
    use Factory;

    /**
     * Test store
     */
    public function testStore()
    {
        $repository = $this->repository(['store']);
        $createdAt  = new CreatedAt(
            $repository,
            'update_date',
            $this->propertyDateTime(User::class, 'updateDate')
        );
        $date       = null;
        $repository->expects($this->exactly(2))
            ->method('store')
            ->with(
                $this->callback(
                    function ($user) use (&$date) {
                        if (is_null($date)) {
                            $date = $user->updateDate;
                        } elseif ($user->updateDate !== $date) {
                            return false;
                        }
                        if (is_null($user->updateDate)) {
                            return false;
                        }
                        if (!$user->updateDate instanceof DateTime) {
                            return false;
                        }

                        return true;
                    }
                )
            );
        $user = new User();
        $createdAt->store($user);
        $this->assertSame($date, $user->updateDate);
        $createdAt->store($user);
        $this->assertSame($date, $user->updateDate);
    }

    /**
     * @param array $methods
     * @return RepositoryInterface|MockObject
     */
    private function repository(array $methods)
    {
        return $this->getMockBuilder(Repository::class)
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock();
    }
}
