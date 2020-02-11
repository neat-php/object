<?php

namespace Neat\Object\Reference;

use Neat\Object\Repository;

class Diff
{
    /** @var Repository */
    private $remoteRepository;

    /** @var array */
    private $after;

    /** @var array */
    private $before;

    /** @var array */
    private $insert = [];

    /** @var array */
    private $update = [];

    /** @var array */
    private $delete = [];

    /**
     * Diff constructor.
     *
     * @param Repository $remoteRepository
     * @param array      $before
     * @param array      $after
     */
    public function __construct(Repository $remoteRepository, array $before, array $after)
    {
        $this->remoteRepository = $remoteRepository;
        $this->before           = $before;
        $this->after            = $after;
        $this->diff();
    }

    /**
     * @return array
     */
    public function getInsert(): array
    {
        return array_values($this->insert);
    }

    /**
     * @return array
     */
    public function getUpdate(): array
    {
        return array_values($this->update);
    }

    /**
     * @return array
     */
    public function getDelete(): array
    {
        return array_values($this->delete);
    }

    /**
     * @return void
     */
    protected function diff()
    {
        $this->insert = array_filter(
            $this->after,
            function ($newObject) {
                foreach ($this->before as $key => $currentObject) {
                    if (!$this->compare($newObject, $currentObject)) {
                        continue;
                    }
                    unset($this->before[$key]);
                    $this->update[] = $newObject;

                    return false;
                }

                return true;
            }
        );

        $this->delete = $this->before;
    }

    /**
     * @param object $entityA
     * @param object $entityB
     * @return bool
     */
    protected function compare($entityA, $entityB): bool
    {
        return $this->remoteRepository->identifier($entityA) === $this->remoteRepository->identifier($entityB);
    }
}
