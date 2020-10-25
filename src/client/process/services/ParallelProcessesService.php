<?php

namespace src\client\process\services;

use src\client\process\ParallelProcess;
use src\client\process\services\ProcessServiceInterface;
use src\client\ResourcePool;
use src\client\settings\SettingsList;
use src\client\process\services\ProcessService;
use src\client\settings\value_object\Settings;

/** Класс для управления параллельными php процессами взаимодействующими через разделяемую память unix.
 * Class ProcessesManager
 * @package src
 */
class ParallelProcessesService extends ProcessService implements ProcessServiceInterface
{
    /**
     * @var array - записи о каналах связи.
     */
    private array $pipes = [];

    /**
     * @var array - запущенные процессы.
     */
    private array $processes = [];

    /**
     * @var array - указатели на каналы связи между процессами.
     */
    private array $processPipes = [];

    /**
     * ParallelProcessesManager constructor.
     * @param SettingsList|null $settingsList
     * @param Settings|null $settings
     */
    public function __construct(?SettingsList $settingsList,
                                ?Settings $settings)
    {
        parent::__construct($settingsList, $settings);
    }

    /** Метод открывает цикл процессов, который передает управление воркерам.
     *  По окончанию выполнения последнего воркера цикл возвращает управление основному процессу.
     * @param ResourcePool $resourcePool
     * @return ParallelProcessesService
     */
    public function startProcessLoop(ResourcePool $resourcePool): ParallelProcessesService
    {
        $this->setResourcePool($resourcePool);
        $workerProcess = $resourcePool->getPoolOfWorkers();

        foreach ($resourcePool->getResourcePool() as $workerName => $configurations) {
            foreach ($configurations as $resourceKey => $value) {
                $numberMemoryKey = $value[1];

                $phpPath = $workerProcess[$workerName]->getPhpPath();
                $process = new ParallelProcess($resourcePool);
                $process->processOpen(
                    $phpPath,
                    $workerName,
                    $resourceKey,
                    $numberMemoryKey,
                    [
                        0 => ['pipe', 'r'],
                        1 => ['pipe', 'w'],
                    ],
                    $process->getResourcePool()
                        ->getPoolOfWorkers()[$workerName]
                        ->getMemorySize(),
                    $this
                );
            }
        }

        return $this;
    }

    /** Метод закрывающий каналы и процессы, открытые для работы.
     * @return ParallelProcessesService
     */
    public function closeProcessLoop(): ParallelProcessesService
    {
        foreach ($this->getResourcePool()->getResourcePool() as $workerName => $configurations) {
            foreach ($configurations as $resourceKey => $value) {
                fclose($this->pipes[$resourceKey][1]);
                fclose($this->pipes[$resourceKey][0]);
                proc_close($this->processes[$resourceKey]);
            }
        }

        $this
            ->getResourcePool()
            ->readAllDataFromResourcePool();

        return $this;
    }

    /** Метод очищает пул ресурсов от данных из воркеров
     * @return bool
     */
    public function clearResourcePool(): bool
    {
        return $this
            ->getResourcePool()
            ->deleteAllDataFromResourcePool();
    }

    /** Метод управляет получение выходных данных из разделяемой памяти по ключу(или всех).
     * @param string|null $workerName - имя воркера
     * @return array
     */
    public function getOutputData(string $workerName = null): array
    {
        return $this
            ->getResourcePool()
            ->getSharedMemory()
            ->getData($workerName);
    }

    /**
     * @return array
     */
    public function &getProcessPipes(): array
    {
        return $this->processPipes;
    }

    /**
     * @return array
     */
    public function getDataManagerForWorkers(): array
    {
        return $this->dataManagerForWorkers;
    }

    /**
     * @param int $processNumber
     * @param array $processPipes
     */
    public function setPipes(int $processNumber,
                             array $processPipes): void
    {
        $this->pipes[$processNumber] = $processPipes;
    }

    /**
     * @param int $processNumber
     * @param $proc
     */
    public function setProcesses(int $processNumber,
                                 $proc): void
    {
        $this->processes[$processNumber] = $proc;
    }

    public function parallel(): void
    {
        $process =
            new ParallelProcess(
                new ResourcePool(
                    $this->getSettingsList(),
                    $this->settings
                )
            );

        $pool = $process->getResourcePool();
        $pool->configureResourcePoolForParallelProcesses($this);

        $this
            ->startProcessLoop($pool)
            ->closeProcessLoop()
            ->clearResourcePool();
    }

    public function single(): void
    {
        $process =
            new ParallelProcess(
                new ResourcePool(
                    null,
                    $this->settings)
            );

        $pool = $process->getResourcePool();
        $pool->configurePoolForSingleProcess($this);

        $this
            ->startProcessLoop($pool)
            ->closeProcessLoop()
            ->clearResourcePool();
    }

}