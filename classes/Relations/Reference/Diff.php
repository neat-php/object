<?php

namespace Neat\Object\Relations\Reference;

use Neat\Object\RepositoryInterface;

/**
 * @template T of object
 */
class Diff
{
    /** @var RepositoryInterface<T> */
    private $remoteRepository;

    /** @var array<T> */
    private $after;

    /** @var array<T> */
    private $before;

    /** @var array<T> */
    private $insert = [];

    /** @var array<T> */
    private $update = [];

    /** @var array<T> */
    private $delete = [];

    /**
     * @param RepositoryInterface<T> $remoteRepository
     * @param array<T>               $before
     * @param array<T>               $after
     */
    public function __construct(RepositoryInterface $remoteRepository, array $before, array $after)
    {
        $this->remoteRepository = $remoteRepository;
        $this->before           = $before;
        $this->after            = $after;
        $this->diff();
    }

    /**
     * @return array<T>
     */
    public function getInsert(): array
    {
        return array_values($this->insert);
    }

    /**
     * @return array<T>
     */
    public function getUpdate(): array
    {
        return array_values($this->update);
    }

    /**
     * @return array<T>
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
     * @param T $entityA
     * @param T $entityB
     * @return bool
     */
    protected function compare(object $entityA, object $entityB): bool
    {
        return $this->remoteRepository->identifier($entityA) === $this->remoteRepository->identifier($entityB);
    }
}
