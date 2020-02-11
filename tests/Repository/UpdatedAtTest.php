<?php

namespace Neat\Object\Test\Repository;

use DateTime;
use Neat\Object\Repository\Repository;
use Neat\Object\Repository\UpdatedAt;
use Neat\Object\Test\Helper\Factory;
use Neat\Object\Test\Helper\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UpdatedAtTest extends TestCase
{
    use Factory;

    /**
     * Test store
     */
    public function testStore()
    {
        $repository = $this->repository(['store']);
        $updatedAt  = new UpdatedAt(
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
                        } elseif ($user->updateDate === $date) {
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
        $updatedAt->store($user);
        $this->assertSame($date, $user->updateDate);
        $updatedAt->store($user);
    }

    /**
     * @param array $methods
     * @return Repository|MockObject
     */
    private function repository(array $methods)
    {
        return $this->getMockBuilder(Repository::class)
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock();
    }
}
