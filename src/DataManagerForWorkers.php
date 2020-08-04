<?php

namespace src;

use src\SharedMemory;

class DataManagerForWorkers
{
    private WorkerProcess $workersSet;

    private array $dataForSet;

    private array $readyChunksOfDataForWorkers;

    public function __construct(WorkerProcess &$workerSet, array $dataForWorkersSet)
    {
        $this->workersSet = $workerSet;
        $this->dataForSet = $dataForWorkersSet;
    }

    public function splitDataForWorkers(): DataManagerForWorkers
    {
        if (($countWorkers = $this->workersSet->getCountWorkers()) == 1) {
            $this->readyChunksOfDataForWorkers[] = $this->dataForSet;
            return $this;
        }

        $arrayChunks = [];
        if (($count = count($this->dataForSet)) % $countWorkers == 0) {
            $set = $count / $countWorkers;
            $arrayChunks = array_chunk($this->dataForSet, $set);
        } else {
            if ($countWorkers > $count) {
                throw new \Exception('Число воркеров не должно превышать количество данных для воркеров');
            }

            $set = (int)floor($count / $countWorkers);
            $arrayChunks = array_chunk($this->dataForSet, $set);

            $lastKey = array_key_last($arrayChunks);
            $preLastKey = $lastKey - 1;

            $result = array_merge($arrayChunks[$preLastKey], $arrayChunks[$lastKey]);
            unset($arrayChunks[$preLastKey], $arrayChunks[$lastKey]);
            $arrayChunks[count($arrayChunks)] = $result;
        }

        $this->readyChunksOfDataForWorkers = $arrayChunks;

        return $this;
    }

    public function putDataIntoWorkerSharedMemory(SharedMemory $sharedMemory) : void
    {
        $resourcePool = $sharedMemory->getResourcePool()[$this->workersSet->getWorkerName()];

        $counter = 0;
        foreach ($resourcePool as $memoryKey => $item) {
            $sharedMemory->write(
                $item[0],
                $this->readyChunksOfDataForWorkers[$counter],
                0
            );
            $counter++;
        }
    }
}