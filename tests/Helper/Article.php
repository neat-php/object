<?php

namespace Neat\Object\Test\Helper;

use Neat\Object\Relations;
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
        /** @var Relations\One $relation */
        $relation = $this->buildBelongsToOne(User::class, 'ArticleBelongsToOneCreator')
            ->referenceFactory(
                function (Relations\Reference\LocalKeyBuilder $builder) {
                    $builder->setLocalKey($builder->property(self::class, 'createdBy'));
                }
            )
            ->resolve();

        return $relation;
    }
}
