<?php

namespace src\client;

use Exception;
use RuntimeException;
use src\client\data_manager\DataManagerForWorkers;
use src\client\data_manager\DataPartitioningStrategy;
use src\client\data_manager\PutDataInJobSharedMemoryStrategy;
use src\client\process\services\AsyncProcessService;
use src\client\process\services\ProcessService;
use src\client\process\value_object\WorkerProcess;
use src\client\settings\SettingsList;
use src\client\settings\value_object\Settings;
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
     * @var SettingsList|null ]
     */
    protected ?SettingsList $settingsList;

    /**
     * @var Settings|null
     */
    protected ?Settings $settings;

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
     * @param SettingsList|null $settingsList
     * @param Settings|null $settings
     */
    public function __construct(?SettingsList $settingsList,
                                ?Settings $settings)
    {
        $this->settingsList = $settingsList;
        $this->settings = $settings;

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
     * @param ProcessService $service
     * @return AsyncProcessService
     */
    public function configurePoolForSingleProcess(ProcessService $service): ProcessService
    {
        $poolOfWorkers = [];
        $poolOfWorkers[$name = $this->settings->getJobName()] =
            new WorkerProcess($first = $this->settings);

        $service->setDataManagerForWorkers(
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
            $service->getDataManagerForWorkers()[$name],
            $this
        );
        $putStrategy->putDataForSingleAsyncProcess();

        return $service;
    }

    /**
     * @param ProcessService $service
     * @return ProcessService
     * @throws \Exception
     */
    public function configureResourcePoolForParallelProcesses(
        ProcessService $service
    ): ProcessService
    {
        $poolOfWorkers = [];
        foreach ($this->settingsList->getList() as $key => $configuration) {
            $poolOfWorkers[$configuration->getJobName()] = new WorkerProcess($configuration);
            $dataPartitioning = $configuration->getDataPartitioning();
            $name = $configuration->getJobName();

            $emptyData = false;
            if (empty($dataPartitioning)) {
                $emptyData = true;
                $service->setDataManagerForWorkers(
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

                $service->setDataManagerForWorkers(
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

            if (isset($dataPartitioning)) {
                $name = $configuration->getJobName();
                $strategy = new PutDataInJobSharedMemoryStrategy(
                    $service->getDataManagerForWorkers()[$name],
                    $this
                );
                $strategy->putData();
            }
        }
        return $service;
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
    public function createResourcePool(array $poolOfWorkers): void
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
    private function addInResourcePool($sharedMemoryResource,
                                       int $sharedMemoryKey,
                                       string $workerName): void
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
        return $this->getResourcePool()[$name] ?? [];
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

    /**
     * @return Settings|null
     */
    public function getSettings(): ?Settings
    {
        return $this->settings;
    }

}