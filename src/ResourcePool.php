<?php

namespace src;

use Exception;
use RuntimeException;
use src\data_manager\DataManagerForWorkers;
use src\data_manager\DataPartitioningStrategy;
use src\data_manager\PutDataInJobSharedMemoryStrategy;
use src\process\process_manager\AsyncProcessManager;
use src\process\process_manager\ProcessManager;
use src\process\object_value\WorkerProcess;
use src\settings\SettingsList;
use src\shared_memory\SharedMemory;
use src\shared_memory\SharedMemoryManager;

/**
 * Class ResourcePool
 * @package src
 */
class ResourcePool
{
    /**
     * @var array - набор воркеров
     */
    private array $poolOfWorkers;

    /**
     * @var SettingsList
     */
    protected SettingsList $settingsList;

    /**
     * @var SharedMemory
     */
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

    /**
     * @var array
     */
    private array $newResourcePool = [];

    /**
     * @var array
     */
    private array $mergedResourcePool = [];

    /**
     * ResourcePool constructor.
     * @param SettingsList $settingsList
     */
    public function __construct(SettingsList $settingsList)
    {
        $this->settingsList = $settingsList;

        $this->SharedMemory = new SharedMemory();
    }

    /**
     * @return SettingsList
     */
    public function getSettingsList(): SettingsList
    {
        return $this->settingsList;
    }

    /**
     * @param AsyncProcessManager $manager
     * @return AsyncProcessManager
     */
    public function configurePoolForSingleProcess(AsyncProcessManager $manager): AsyncProcessManager
    {
        $poolOfWorkers = [];
        $poolOfWorkers[$name = $this->settingsList->getFirst()->getJobName()] =
            new WorkerProcess($first = $this->settingsList->getFirst());

        $manager->setDataManagerForWorkers(
            $name,
            $dataManager =
                new DataManagerForWorkers(
                    $poolOfWorkers[$name],
                    $first->getData(),
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

    /**
     * @param ProcessManager $manager
     * @return ProcessManager
     * @throws \Exception
     */
    public function configureResourcePoolForParallelProcesses(
        ProcessManager $manager
    ): ProcessManager
    {
        $poolOfWorkers = [];
        foreach ($this->settingsList->getList() as $key => $configuration) {
            $poolOfWorkers[$configuration->getJobName()] = new WorkerProcess($configuration);
            $dataPartitioning = $configuration->getDataPartitioning();
            $name = $configuration->getJobName();

            $emptyData = false;
            if (empty($dataPartitioning)) {
                $emptyData = true;
                $manager->setDataManagerForWorkers(
                    $name,
                    $dataManager =
                        new DataManagerForWorkers(
                            $poolOfWorkers[$name],
                            $dataPartitioning,
                            $this->SharedMemory
                        )
                );
            }

            if (isset($dataPartitioning) && !$emptyData) {
                try {
                    if (!isset($dataPartitioning["flagPartitioning"]) ||
                        $dataPartitioning["flagPartitioning"] === null ||
                        count($dataPartitioning) == 1) {
                        throw new RuntimeException('The data separator flag for workers was not specified.');
                    }
                } catch (Exception $e) {
                    exit($e->getMessage());
                }

                $manager->setDataManagerForWorkers(
                    $name,
                    $dataManager =
                        new DataManagerForWorkers(
                            $poolOfWorkers[$name],
                            $dataPartitioning,
                            $this->SharedMemory
                        )
                );

                $strategy = new DataPartitioningStrategy($dataManager);
                $strategy->prepareDataForRecording();
            }
        }

        $this->poolOfWorkers = &$poolOfWorkers;

        $this->createResourcePool($this->poolOfWorkers);

        foreach ($this->settingsList->getList() as $key => $configuration) {
            $dataPartitioning = $configuration->getDataPartitioning();

            if (isset($dataPartitioning) && !empty($dataPartitioning)) {
                $name = $configuration->getJobName();
                $strategy = new PutDataInJobSharedMemoryStrategy(
                    $manager->getDataManagerForWorkers()[$name],
                    $this
                );
                $strategy->putData();
            }
        }
        return $manager;
    }

    /**
     * @return SharedMemory
     */
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
                $sharedMemoryResource =
                    SharedMemoryManager::openShResource(
                        $this->SharedMemory,
                        array(
                            "sharedMemoryKey" => $sharedMemoryKey,
                            "openFlag" => "n",
                            "shSize" => $memorySize
                        )
                    );

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
     * @param string $workerName
     */
    private function addInResourcePool(
        $sharedMemoryResource,
        int $sharedMemoryKey,
        string $workerName
    ): void
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
        $this
            ->createNewResourcePool()
            ->mergedResourcePool();

        foreach ($this->mergedResourcePool as $workerName => $configations) {
            foreach ($configations as $key => $value) {
                $memoryResource = $value[0];
                $read = SharedMemoryManager::readShResource(
                    $this->SharedMemory,
                    array(
                        "sharedMemoryResource" => $memoryResource,
                        "start" => 0,
                        "size" => shmop_size($memoryResource) - 0
                    )
                );

                $data = unserialize($read);
                $data = $data === false ? null : $data;

                $this->SharedMemory->setOutputElementByKey($workerName, $key, $data, $value);
            }
        }

        return $this->SharedMemory->getData();
    }

    /** Метод освобождает все ресурсы разделяемой памяти занятые во время выполнения
     *  и удаляет соответствующие записи в наборе ресурсов.
     * @return bool
     */
    public function deleteAllDataFromResourcePool(): bool
    {
        foreach ($this->mergedResourcePool as $workerName => $configations) {
            foreach ($configations as $key => $value) {
                $memoryResource = $value[0];
                $delete = SharedMemoryManager::deleteSh(
                    $this->SharedMemory,
                    $memoryResource
                );

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

    /**
     * @param string $name
     * @return array
     */
    public function getResourceByJobName(string $name): array
    {
        return $this->getResourcePool()[$name];
    }

    /**
     * @return array
     */
    public function getPoolOfWorkers(): array
    {
        return $this->poolOfWorkers;
    }

    /**
     * @return $this
     */
    private function createNewResourcePool(): self
    {
        $newResourcePoll = [];
        foreach ($this->resourcePool as $workerName => $configations) {
            foreach ($configations as $key => $value) {
                $poolConf = $this->resourcePoolСonfirations;
                $open = SharedMemoryManager::openShResource(
                    $this->SharedMemory,
                    array(
                        "sharedMemoryKey" => $value[1],
                        "openFlag" => "a",
                        "shSize" => $poolConf[$workerName]["memorySize"]
                    )
                );
                $newResourcePoll[$workerName][$key] = [$open, $value[1]];
            }
        }
        $this->newResourcePool = $newResourcePoll;

        return $this;
    }

    private function mergedResourcePool(): void
    {
        $merged = [];
        foreach ($this->resourcePool as $name => $value) {
            foreach ($value as $oldKey => $oldValue) {
                if (array_key_exists($name, $this->newResourcePool)) {
                    if (array_key_exists($oldKey, $this->newResourcePool[$name])) {
                        $merged[$name][$oldKey] = $this->newResourcePool[$name][$oldKey];
                        continue;
                    }
                }
                $merged[$name][$oldKey] = $oldValue;
            }
        }
        $this->mergedResourcePool = $merged;
    }

}