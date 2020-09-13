<?php

namespace src\data_manager;

class DataPartitioningStrategy
{
    private DataManagerForWorkers $manager;

    public function __construct(DataManagerForWorkers $dataManagerForWorkers)
    {
        $this->manager = $dataManagerForWorkers;
    }

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