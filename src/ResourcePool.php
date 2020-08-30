<?php

namespace src;

use src\data_manager\DataManagerForWorkers;
use src\data_manager\DataPartitioningStrategy;
use src\data_manager\PutDataInJobSharedMemoryStrategy;
use src\process\AsyncProcessManager;
use src\process\WorkerProcess;
use src\settings\Settings;

class ResourcePool
{
    /**
     * @var array - набор воркеров
     */
    private array $poolOfWorkers;

    protected Settings $settings;

    private SharedMemory $SharedMemory;


    public function __construct(Settings $settings)
    {
        $this->settings = $settings;

        $this->SharedMemory = new SharedMemory();
    }

    public function getSettingsForSingleProcess(): array
    {
        return $this->settings->getSettingsObjects()[0]->getJobTypeSettings();
    }

    public function configurePoolForSingleProcess(AsyncProcessManager $manager): AsyncProcessManager
    {
        $poolOfWorkers = [];
        $poolOfWorkers[$name = $this->getSettingsForSingleProcess()["jobName"]] =
            new WorkerProcess($this->getSettingsForSingleProcess());

        $manager->setDataManagerForWorkers(
            $name,
            $dataManager =
                new DataManagerForWorkers(
                    $poolOfWorkers[$name],
                    $this->getSettingsForSingleProcess()['data']
                )
        );

        $this->poolOfWorkers = &$poolOfWorkers;
        $this->SharedMemory->createResourcePool($this->poolOfWorkers);

        $partitioningStrategy = new DataPartitioningStrategy($dataManager);
        $partitioningStrategy->writeDadaForSingleAsyncProcess();

        $putStrategy = new PutDataInJobSharedMemoryStrategy(
            $manager->getDataManagerForWorkers()[$name],
            $this->SharedMemory
        );
        $putStrategy->putDataForSingleAsyncProcess();

        return $manager;
    }

    public function getSharedMemory() : SharedMemory
    {
        return $this->SharedMemory;
    }
}