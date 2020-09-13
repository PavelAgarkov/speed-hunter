<?php

namespace src\data_manager;

use src\ResourcePool;

class PutDataInJobSharedMemoryStrategy
{
    private DataManagerForWorkers $manager;

    private ResourcePool $resourcePool;

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