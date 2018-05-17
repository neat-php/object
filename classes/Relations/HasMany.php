<?php


namespace Neat\Object\Relations;


use Neat\Object\Entity;

class HasMany
{
    /**
     * The parent object
     *
     * @var Entity
     */
    private $parent;

    /**
     * The related class
     *
     * @var string
     */
    private $related;

    /**
     * @var string
     */
    private $foreignKey;

    /**
     * @var bool|Entity[]
     */
    private $items = false;

    public function __construct(Entity $parent, string $related, string $foreignKey)
    {
        $this->parent = $parent;
        $this->related = $related;
        $this->foreignKey = $foreignKey;
    }

    public function save()
    {
        if (!$this->items) {
            return;
        }
        foreach ($this->items as $item) {
            $item->store();
        }
    }
}
