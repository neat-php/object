<?php

namespace Neat\Object\Test\Helper;

use Neat\Object\Identifier;
use Neat\Object\Relations;
use Neat\Object\Storage;

class Article
{
    use Identifier;
    use Storage;
    use Relations;

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
            ->referenceFactory(function (Relations\Reference\LocalKeyBuilder $builder) {
                $builder->setLocalKey('createdBy');
            })
            ->resolve();

        return $relation;
    }
}
