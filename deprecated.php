<?php
namespace Neat\Object\Decorator {
    /** @deprecated */
    class CreatedAt extends \Neat\Object\Repository\CreatedAt {}
    /** @deprecated */
    class SoftDelete extends \Neat\Object\Repository\SoftDelete {}
    /** @deprecated */
    class TimeStamp extends \Neat\Object\Repository\TimeStamp {}
    /** @deprecated */
    class UpdatedAt extends \Neat\Object\Repository\UpdatedAt {}
}
namespace Neat\Object {
    /** @deprecated */
    trait RepositoryDecorator { use Repository\RepositoryDecorator; }
    /** @deprecated */
    interface RepositoryInterface extends Repository {}
    /** @deprecated */
    trait ReferenceFactory { use Reference\ReferenceFactory; }
}
namespace Neat\Object\Relations {
    /** @deprecated */
    interface Reference extends \Neat\Object\Reference {}
    /** @deprecated */
    abstract class Relation extends \Neat\Object\Relation {}
    /** @deprecated */
    interface ReferenceBuilder extends \Neat\Object\Reference\ReferenceBuilder {}
    /** @deprecated */
    class Many extends \Neat\Object\Relation\Many {}
    /** @deprecated */
    class One extends \Neat\Object\Relation\One {}
    /** @deprecated */
    class RelationBuilder extends \Neat\Object\Relation\RelationBuilder {}
}
namespace Neat\Object\Relations\Reference {
    /** @deprecated */
    trait Builder { use \Neat\Object\Reference\Builder; }
    /** @deprecated */
    class Diff extends \Neat\Object\Reference\Diff {}
    /** @deprecated */
    class JunctionTable extends \Neat\Object\Reference\JunctionTable {}
    /** @deprecated */
    class JunctionTableBuilder extends \Neat\Object\Reference\JunctionTableBuilder {}
    /** @deprecated */
    class LocalKey extends \Neat\Object\Reference\LocalKey {}
    /** @deprecated */
    class LocalKeyBuilder extends \Neat\Object\Reference\LocalKeyBuilder {}
    /** @deprecated */
    class RemoteKey extends \Neat\Object\Reference\RemoteKey {}
    /** @deprecated */
    class RemoteKeyBuilder extends \Neat\Object\Reference\RemoteKeyBuilder {}
}
