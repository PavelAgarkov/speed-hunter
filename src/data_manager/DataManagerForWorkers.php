<?php

namespace src\data_manager;

use Exception;
use RuntimeException;
use src\process\object_value\WorkerProcess;
use src\ResourcePool;
use src\shared_memory\SharedMemory;
use src\shared_memory\SharedMemoryManager;

/** Класс реализует запись передаваемых в воркер данных в разделяемую
 *  память предназначенную для набора воркров.
 * Class DataManagerForWorkers
 * @package src
 */
class DataManagerForWorkers
{
    /**
     * @var WorkerProcess - набор однотипных процессов
     */
    private WorkerProcess $workersSet;

    /**
     * @var array - неподготовленный набор данных для записи в воркеры
     */
    private array $dataForSet;

    /**
     * @var array - подготовленные наборы данных для воркеров
     */
    private array $readyChunksOfDataForWorkers;

    /**
     * @var SharedMemory
     */
    private SharedMemory $SharedMemory;

    /**
     * DataManagerForWorkers constructor.
     * @param WorkerProcess $workerSet
     * @param array $dataForWorkersSet
     * @param SharedMemory $sharedMemory
     */
    public function __construct(
        WorkerProcess $workerSet,
        array $dataForWorkersSet,
        SharedMemory $sharedMemory
    ) {
        $this->workersSet = $workerSet;
        $this->dataForSet = $dataForWorkersSet;
        $this->SharedMemory = $sharedMemory;
    }

    /** Метод разбивает на "куски" неподготовленные данные для всех воркеров,
     *  в зависимости от их количества
     * @return $this
     * @throws RuntimeException
     */
    public function splitDataForWorkers(): DataManagerForWorkers
    {
        if (($countWorkers = $this->workersSet->getCountWorkers()) == 1) {
            $this->readyChunksOfDataForWorkers[] = $this->dataForSet[1];
            return $this;
        }

        $arrayChunks = [];
        if (($count = count($this->dataForSet["dataToPartitioning"])) % $countWorkers == 0) {
            $set = $count / $countWorkers;
            $arrayChunks = array_chunk($this->dataForSet["dataToPartitioning"], $set);
        } else {
            try {
                if ($countWorkers > $count) {
                    throw new RuntimeException(
                        'The number of workers should not exceed the number of arrays for them.'
                    );
                }
            } catch (Exception $e) {
                exit($e->getMessage());
            }

            if($count % $countWorkers == 1) {
                $set = (int)floor($count / $countWorkers);
                $arrayChunks = array_chunk($this->dataForSet["dataToPartitioning"], $set);

                $lastKey = array_key_last($arrayChunks);
                $preLastKey = $lastKey - 1;

                $result = array_merge($arrayChunks[$preLastKey], $arrayChunks[$lastKey]);
                unset($arrayChunks[$preLastKey], $arrayChunks[$lastKey]);
                $arrayChunks[count($arrayChunks)] = $result;
            }

            if($count % $countWorkers == 2) {
                $arrayChunks = array_chunk($this->dataForSet["dataToPartitioning"], 1);

                $lastKey = array_key_last($arrayChunks);
                $preLastKey = $lastKey - 1;
                $beforePreLastKey = $preLastKey - 1;

                $result = array_merge(
                    $arrayChunks[$preLastKey],
                    $arrayChunks[$lastKey],
                    $arrayChunks[$beforePreLastKey]
                );
                unset(
                    $arrayChunks[$preLastKey],
                    $arrayChunks[$lastKey],
                    $arrayChunks[$beforePreLastKey]
                );
                $arrayChunks[count($arrayChunks)] = $result;
            }
        }

        $this->readyChunksOfDataForWorkers = $arrayChunks;

        return $this;
    }

    /** Метод записывает подготовленные "куски" данных в подготовленную
     *  разделяемую память по ключам
     * @param ResourcePool $resourcePool
     */
    public function putDataIntoWorkerSharedMemory(ResourcePool $resourcePool): void
    {
        $resourcePool = $resourcePool->getResourcePool()[$this->workersSet->getWorkerName()];

        $counter = 0;
        foreach ($resourcePool as $memoryKey => $item) {
            SharedMemoryManager::writeIntoSh(
                $this->SharedMemory,
                $item[0],
                $this->readyChunksOfDataForWorkers[$counter]
            );
            $counter++;
        }
    }

    /** Метод для записи данных без разделения по количеству воркеров, если в конфигурационном
     *  массиве указан параметр 0 => false
     * @param ResourcePool $resourcePool
     */
    public function putCommonDataIntoWorkers(ResourcePool $resourcePool): void
    {
        $resourcePool = $resourcePool->getResourcePool()[$this->workersSet->getWorkerName()];
        foreach ($resourcePool as $memoryKey => $item) {
            SharedMemoryManager::writeIntoSh(
                $this->SharedMemory,
                $item[0],
                $this->readyChunksOfDataForWorkers
            );
        }
    }

    /** Метод записывает в участок разделяемой памяти одни данные для всех воркеров
     * @return $this
     */
    public function passCommonDataForAllWorkers(): DataManagerForWorkers
    {
        $this->readyChunksOfDataForWorkers = $this->dataForSet["dataToPartitioning"];

        return $this;
    }

    /**
     * @return $this
     */
    public function passDataForSingleAsyncProcess(): DataManagerForWorkers
    {
        $this->readyChunksOfDataForWorkers = $this->dataForSet;

        return $this;
    }

    /**
     * @return array
     */
    public function getDataForSet(): array
    {
        return $this->dataForSet;
    }
}