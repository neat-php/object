<?php

namespace Neat\Object\Relations\Reference;

use Neat\Object\Repository;
use Neat\Object\RepositoryInterface;

class Diff
{
    /**
     * @var Repository
     */
    private $remoteRepository;

    /**
     * @var array
     */
    private $new;

    /**
     * @var array
     */
    private $current;

    /**
     * @var array
     */
    private $insert = [];

    /**
     * @var array
     */
    private $update = [];

    /**
     * @var array
     */
    private $delete = [];

    /**
     * Diff constructor.
     * @param RepositoryInterface $remoteRepository
     * @param array               $new
     * @param array               $current
     */
    public function __construct(RepositoryInterface $remoteRepository, array $new, array $current)
    {
        $this->remoteRepository = $remoteRepository;
        $this->new              = $new;
        $this->current          = $current;
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

    protected function diff()
    {
        $this->insert = array_filter($this->new, function ($newObject) {
            foreach ($this->current as $key => $currentObject) {
                if (!$this->compare($newObject, $currentObject)) {
                    continue;
                }
                unset($this->current[$key]);
                $this->update[] = $newObject;

                return false;
            }

            return true;
        });

        $this->delete = $this->current;
    }

    /**
     * @param object $entityA
     * @param object $entityB
     * @return bool
     */
    protected function compare($entityA, $entityB): bool
    {
        return $this->remoteRepository->identifier($entityA) == $this->remoteRepository->identifier($entityB);
    }
}
