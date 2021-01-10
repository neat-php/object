<?php

namespace Neat\Object\Relations\Reference;

use Neat\Object\RepositoryInterface;

class Diff
{
    /** @var RepositoryInterface */
    private $remoteRepository;

    /** @var array<object> */
    private $after;

    /** @var array<object> */
    private $before;

    /** @var array<object> */
    private $insert = [];

    /** @var array<object> */
    private $update = [];

    /** @var array<object> */
    private $delete = [];

    /**
     * Diff constructor.
     *
     * @param RepositoryInterface $remoteRepository
     * @param array<object>       $before
     * @param array<object>       $after
     */
    public function __construct(RepositoryInterface $remoteRepository, array $before, array $after)
    {
        $this->remoteRepository = $remoteRepository;
        $this->before           = $before;
        $this->after            = $after;
        $this->diff();
    }

    /**
     * @return array<object>
     */
    public function getInsert(): array
    {
        return array_values($this->insert);
    }

    /**
     * @return array<object>
     */
    public function getUpdate(): array
    {
        return array_values($this->update);
    }

    /**
     * @return array<object>
     */
    public function getDelete(): array
    {
        return array_values($this->delete);
    }

    /**
     * @return void
     */
    protected function diff(): void
    {
        $this->insert = array_filter(
            $this->after,
            function (object $newObject) {
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
    protected function compare(object $entityA, object $entityB): bool
    {
        return $this->remoteRepository->identifier($entityA) === $this->remoteRepository->identifier($entityB);
    }
}
