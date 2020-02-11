<?php

namespace Neat\Object\Test\Helper;

class GroupUser extends Entity
{
    const KEY = ['user_id', 'group_id'];

    /** @var int */
    public $userId;

    /** @var int */
    public $groupId;
}
