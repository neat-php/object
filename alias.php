<?php

// Moved Neat\Object\Decorator namespace to Neat\Object\Repository.
class_alias(Neat\Object\Repository\CreatedAt::class, Neat\Object\Decorator\CreatedAt::class);
class_alias(Neat\Object\Repository\SoftDelete::class, Neat\Object\Decorator\SoftDelete::class);
class_alias(Neat\Object\Repository\TimeStamp::class, Neat\Object\Decorator\TimeStamp::class);
class_alias(Neat\Object\Repository\UpdatedAt::class, Neat\Object\Decorator\UpdatedAt::class);

// Moved Neat\Object\RepositoryDecorator to Neat\Object\Repository\RepositoryDecorator
class_alias(Neat\Object\Repository\RepositoryDecorator::class, Neat\Object\RepositoryDecorator::class);

// Moved Neat\Object\Repository class to Neat\Object\Repository\Repository.
// NOTE: CANNOT ALIAS THIS CLASS BECAUSE THE OLD NAME IS NOW TAKEN BY THE INTERFACE INSTEAD

// Moved Neat\Object\RepositoryInterface interface to Neat\Object\Repository.
class_alias(Neat\Object\Repository::class, Neat\Object\RepositoryInterface::class);

// Moved Neat\Object\Relations\Reference namespace and class to Neat\Object\Reference.
class_alias(Neat\Object\Reference::class, Neat\Object\Relations\Reference::class);
class_alias(Neat\Object\Reference\Builder::class, Neat\Object\Relations\Reference\Builder::class);
class_alias(Neat\Object\Reference\Diff::class, Neat\Object\Relations\Reference\Diff::class);
class_alias(Neat\Object\Reference\JunctionTable::class, Neat\Object\Relations\Reference\JunctionTable::class);
class_alias(Neat\Object\Reference\JunctionTableBuilder::class, Neat\Object\Relations\Reference\JunctionTableBuilder::class);
class_alias(Neat\Object\Reference\LocalKey::class, Neat\Object\Relations\Reference\LocalKey::class);
class_alias(Neat\Object\Reference\LocalKeyBuilder::class, Neat\Object\Relations\Reference\LocalKeyBuilder::class);
class_alias(Neat\Object\Reference\RemoteKey::class, Neat\Object\Relations\Reference\RemoteKey::class);
class_alias(Neat\Object\Reference\RemoteKeyBuilder::class, Neat\Object\Relations\Reference\RemoteKeyBuilder::class);

// Moved Neat\Object\Relations\Relation class to Neat\Object\Relation.
class_alias(Neat\Object\Relation::class, Neat\Object\Relations\Relation::class);

// Moved Neat\Object\Relations\ReferenceBuilder to Neat\Object\Reference\ReferenceBuilder
class_alias(Neat\Object\Reference\ReferenceBuilder::class, Neat\Object\Relations\ReferenceBuilder::class);

// Moved Neat\Object\Relations namespace to Neat\Object\Relation.
class_alias(Neat\Object\Relation\Many::class, Neat\Object\Relations\Many::class);
class_alias(Neat\Object\Relation\One::class, Neat\Object\Relations\One::class);
class_alias(Neat\Object\Relation\RelationBuilder::class, Neat\Object\Relations\RelationBuilder::class);

// Moved Neat\Object\ReferenceFactory class to Neat\Object\Reference\ReferenceFactory.
class_alias(Neat\Object\Reference\ReferenceFactory::class, Neat\Object\ReferenceFactory::class);
