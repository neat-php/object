<?php

/** @noinspection PhpLanguageLevelInspection */

namespace Neat\Object\Test\Helper;

use DateTime;
use DateTimeImmutable;

class Contact
{
    public int $id;
    public string $email;
    public ?Phone $phone;
    public $data;
    public bool $default;
    public DateTime $createdAt;
    public ?DateTimeImmutable $deletedAt;
}
