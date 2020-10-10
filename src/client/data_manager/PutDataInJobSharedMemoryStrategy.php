<?php

namespace src\client\data_manager;

use src\client\ResourcePool;

/**
 * Class PutDataInJobSharedMemoryStrategy
 * @package src\data_manager
 */
class PutDataInJobSharedMemoryStrategy
{
    /**
     * @var DataManagerForWorkers
     */
    private DataManagerForWorkers $manager;

    /**
     * @var ResourcePool
     */
    private ResourcePool $resourcePool;

    /**
     * PutDataInJobSharedMemoryStrategy constructor.
     * @param DataManagerForWorkers $dataManagerForWorkers
     * @param ResourcePool $resourcePool
     */
    public function __construct(
        DataManagerForWorkers $dataManagerForWorkers,
        ResourcePool $resourcePool
    ) {
        $this->manager = $dataManagerForWorkers;
        $this->resourcePool = $resourcePool;
    }

    public function putData(): void
    {
        if ((int)$this->manager->getDataForSet()['flagPartitioning'] == 1) {
            $this->manager->putDataIntoWorkerSharedMemory($this->resourcePool);
        } else {
            $this->manager->putCommonDataIntoWorkers($this->resourcePool);
        }
    }

    public function putDataForSingleAsyncProcess(): void
    {
        $this->manager->putCommonDataIntoWorkers($this->resourcePool);
    }

}