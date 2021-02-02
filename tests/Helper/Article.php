<?php

namespace Neat\Object\Test\Helper;

use Neat\Object\Relations;
use Neat\Object\Relations\Reference\LocalKeyBuilder;
use Neat\Object\Storage;

class Article
{
    use Storage;
    use Relations;

    /** @var int */
    public $id;

    /** @var string */
    public $name;

    /** @var int */
    public $createdBy;

    /** @var int */
    public $updatedBy;

    /** @var int */
    public $deletedBy;

    public function creator(): Relations\One
    {
        return $this->belongsToOne(User::class, __FUNCTION__, function (LocalKeyBuilder $builder) {
            $builder->setLocalKey('createdBy');
        });
    }
}
