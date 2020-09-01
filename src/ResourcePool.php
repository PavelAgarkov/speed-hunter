<?php

namespace src;

use src\data_manager\DataManagerForWorkers;
use src\data_manager\DataPartitioningStrategy;
use src\data_manager\PutDataInJobSharedMemoryStrategy;
use src\process\AsyncProcessManager;
use src\process\ParallelProcessesManager;
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

    /**
     * @var array - набор ресурсов для записи дополнительных настроек
     */
    private array $resourcePoolСonfirations = [];

    /**
     * @var array - закрытый массив для записи ресурсов, представляющих собой информацию
     * о созданных участках разделяемой памяти.
     */
    private array $resourcePool = [];

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
                    $this->getSettingsForSingleProcess()['data'],
                    $this->SharedMemory
                )
        );

        $this->poolOfWorkers = &$poolOfWorkers;
        $this->createResourcePool($this->poolOfWorkers);

        $partitioningStrategy = new DataPartitioningStrategy($dataManager);
        $partitioningStrategy->writeDadaForSingleAsyncProcess();

        $putStrategy = new PutDataInJobSharedMemoryStrategy(
            $manager->getDataManagerForWorkers()[$name],
            $this
        );
        $putStrategy->putDataForSingleAsyncProcess();

        return $manager;
    }

    public function configureResourcePoolForParallelProcesses(ParallelProcessesManager $manager
    ): ParallelProcessesManager {
        $poolOfWorkers = [];
        foreach ($this->settings->getSettingsObjects() as $key => $configuration) {
            $jobSettings = $configuration->getJobTypeSettings();
            $poolOfWorkers[$jobSettings["jobName"]] = new WorkerProcess($jobSettings);

            if (isset($jobSettings["dataPartitioning"])) {
                try {
                    if (!isset($jobSettings["dataPartitioning"]["flagPartitioning"]) ||
                        $jobSettings["dataPartitioning"]["flagPartitioning"] === null ||
                        count($jobSettings["dataPartitioning"]) == 1) {
                        throw new \RuntimeException('The data separator flag for workers was not specified.');
                    }
                } catch (\Exception $e) {
                    exit($e->getMessage());
                }

                $name = $jobSettings["jobName"];
                $manager->setDataManagerForWorkers(
                    $name,
                    $dataManager =
                        new DataManagerForWorkers(
                            $poolOfWorkers[$name],
                            $jobSettings['dataPartitioning'],
                            $this->SharedMemory
                        )
                );

                $strategy = new DataPartitioningStrategy($dataManager);
                $strategy->prepareDataForRecording();
            }
        }

        $this->poolOfWorkers = &$poolOfWorkers;

        $this->createResourcePool($this->poolOfWorkers);

        foreach ($this->settings->getSettingsObjects() as $key => $configuration) {
            $jobSettings = $configuration->getJobTypeSettings();

            if (isset($jobSettings["dataPartitioning"])) {
                $name = $jobSettings["jobName"];
                $strategy = new PutDataInJobSharedMemoryStrategy(
                    $manager->getDataManagerForWorkers()[$name],
                    $this
                );
                $strategy->putData();
            }
        }

        return $manager;
    }

    public function getSharedMemory(): SharedMemory
    {
        return $this->SharedMemory;
    }

    /** Метод создает набор участков разделяемой памяти и записываеи их в массив.
     * @param array $poolOfWorkers
     */
    public function createResourcePool(array $poolOfWorkers)
    {
        foreach ($poolOfWorkers as $key => $worker) {
            $workerName = $worker->getWorkerName();
            $count = $worker->getCountWorkers();
            $memorySize = $worker->getMemorySize();

            $this->resourcePoolСonfirations[$workerName]['workerName'] = $workerName;
            $this->resourcePoolСonfirations[$workerName]['countWorkers'] = $count;
            $this->resourcePoolСonfirations[$workerName]['memorySize'] = $memorySize;

            $this->resourcePool[$workerName] = [];


            while (count($this->resourcePool[$workerName]) < $count) {
                //  ключ разделяемой памяти
                $sharedMemoryKey = rand(100, 9000000);
                //флаг "n" говорит, что создается новый участок общей памяти и возвращает false если участок с таким ключом уже есть
                // permission 0755
                $sharedMemoryResource = $this->SharedMemory->open($sharedMemoryKey, "n", $memorySize);

                // если разделяемая память по данному ключу занята, то делаем новый ключ, иначе записываем в пул памяти
                $this->addInResourcePool($sharedMemoryResource, $sharedMemoryKey, $workerName);
            }
        }
    }

    /** Метод проверяющий занят ли участок разделяемой памяти с данным ключом.
     *  Если участок занят, то формируется новый ключ из случайного интервала.
     *  Если участок свободен, то информация о созданном участке записывается
     *  в массив.
     * @param resource $sharedMemoryResource - ресурс разделяемой памяти.
     * @param int $sharedMemoryKey - ключ разделяемой памяти.
     */
    private function addInResourcePool($sharedMemoryResource, int $sharedMemoryKey, string $workerName): void
    {
        if ($sharedMemoryResource === false) {
            $sharedMemoryKey = rand(100, 9000000);
        } else {
            $memory = (string)$sharedMemoryResource;
            $memoryNumber = preg_replace('/[^0-9]/', '', explode(' ', $memory)[2]);
            $this->resourcePool[$workerName][$memoryNumber] = [
                $sharedMemoryResource,
                $sharedMemoryKey
            ];
        }
    }

    /** Метод читает из всех ресурсов и записывает в выходной массив.
     * @return array
     */
    public function readAllDataFromResourcePool(): array
    {
        $sharedMemory = $this->getSharedMemory();
        foreach ($this->getResourcePool() as $workerName => $configations) {
            foreach ($configations as $key => $value) {
                $memoryResource = $value[0];
                $read = $sharedMemory->read($memoryResource, 0, shmop_size($memoryResource) - 0);
                $data = unserialize($read);
                $sharedMemory->setOutputElementByKey($workerName, $key, $data);
            }
        }

        return $sharedMemory->getData();
    }

    /** Метод освобождает все ресурсы разделяемой памяти занятые во время выполнения
     *  и удаляет соответствующие записи в наборе ресурсов.
     * @return bool
     */
    public function deleteAllDataFromResourcePool(): bool
    {
        $sharedMemory = $this->getSharedMemory();
        foreach ($this->getResourcePool() as $workerName => $configations) {
            foreach ($configations as $key => $value) {
                $memoryResource = $value[0];
                $delete = $sharedMemory->delete($memoryResource);
                if ($delete === true) {
                    unset($this->resourcePool[$workerName][$key]);
                }
            }
        }

        return !empty($this->getResourcePool());
    }

    /** Метод возвращает набор ресурсов.
     * @return array
     */
    public function getResourcePool(): array
    {
        return $this->resourcePool;
    }

    public function getResourceByJobName(string $name): array
    {
        return $this->getResourcePool()[$name];
    }

    public function getPoolOfWorkers(): array
    {
        return $this->poolOfWorkers;
    }
}