<?php

namespace Neat\Object\Test\Helper;

use Neat\Object\Relation;
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

    public function creator(): Relation\One
    {
        /** @var Relation\One $relation */
        $relation = $this->buildBelongsToOne(User::class, 'ArticleBelongsToOneCreator')
            ->referenceFactory(
                function (\Neat\Object\Reference\LocalKeyBuilder $builder) {
                    $builder->setLocalKey($builder->property(self::class, 'createdBy'));
                }
            )
            ->resolve();

        return $relation;
    }
}
