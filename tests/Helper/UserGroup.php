<?php

namespace Neat\Object\Test\Helper;

class UserGroup extends Entity
{
    const KEY = ['user_id', 'group_id'];

    /**
     * @var int
     */
    public $userId;

    /**
     * @var int
     */
    public $groupId;
}
