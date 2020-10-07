<?php

namespace src\data_manager;

/**
 * Class DataPartitioningStrategy
 * @package src\data_manager
 */
class DataPartitioningStrategy
{
    /**
     * @var DataManagerForWorkers
     */
    private DataManagerForWorkers $manager;

    /**
     * DataPartitioningStrategy constructor.
     * @param DataManagerForWorkers $dataManagerForWorkers
     */
    public function __construct(DataManagerForWorkers $dataManagerForWorkers)
    {
        $this->manager = $dataManagerForWorkers;
    }

    /**
     * @throws \Exception
     */
    public function prepareDataForRecording(): void
    {
        if ((int)$this->manager->getDataForSet()['flagPartitioning'] == 1) {
            $this->manager->splitDataForWorkers();
        } else {
            $this->manager->passCommonDataForAllWorkers();
        }
    }

    public function writeDadaForSingleAsyncProcess(): void
    {
        $this->manager->passDataForSingleAsyncProcess();
    }

}