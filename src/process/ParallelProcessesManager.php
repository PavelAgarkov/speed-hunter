<?php

namespace src\process;

use src\process\ProcessManagerInterface;
use src\ResourcePool;
use src\settings\SettingsList;

/** Класс для управления параллельными php процессами взаимодействующими через разделяемую память unix.
 * Class ProcessesManager
 * @package src
 */
class ParallelProcessesManager extends ProcessManager implements ProcessManagerInterface
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
     * @var array
     */
    private array $readDataFromSubProc = [];

    /**
     * ParallelProcessesManager constructor.
     * @param SettingsList $settingsList
     */
    public function __construct(SettingsList $settingsList)
    {
        parent::__construct($settingsList);
    }

    /** Метод открывает цикл процессов, который передает управление воркерам.
     *  По окончанию выполнения последнего воркера цикл возвращает управление основному процессу.
     * @param ResourcePool $resourcePool
     * @return ParallelProcessesManager
     */
    public function startProcessLoop(ResourcePool $resourcePool): ParallelProcessesManager
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
                    $process->getResourcePool()->getPoolOfWorkers()[$workerName]->getMemorySize(),
                    $this
                );
            }
        }

        return $this;
    }

    /** Метод закрывающий каналы и процессы, открытые для работы.
     * @return ParallelProcessesManager
     */
    public function closeProcessLoop(): ParallelProcessesManager
    {
        foreach ($this->getResourcePool()->getResourcePool() as $workerName => $configurations) {
            foreach ($configurations as $resourceKey => $value) {
                fclose($this->pipes[$resourceKey][1]);
                fclose($this->pipes[$resourceKey][0]);
                proc_close($this->processes[$resourceKey]);
            }
        }

        $this->getResourcePool()->readAllDataFromResourcePool();

        return $this;
    }

    /** Метод очищает пул ресурсов от данных из воркеров
     * @return bool
     */
    public function clearResourcePool(): bool
    {
        return $this->getResourcePool()->deleteAllDataFromResourcePool();
    }

    /** Метод управляет получение выходных данных из разделяемой памяти по ключу(или всех).
     * @param string|null $workerName - имя воркера
     * @return array
     */
    public function getOutputData(string $workerName = null): array
    {
        return $this->getResourcePool()->getSharedMemory()->getData($workerName);
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
     * @return array
     */
    public function getPipes(int $processNumber): array
    {
        return $this->pipes[$processNumber];
    }

    /**
     * @param int $processNumber
     * @param array $processPipes
     */
    public function setPipes(int $processNumber, array $processPipes): void
    {
        $this->pipes[$processNumber] = $processPipes;
    }

    /**
     * @param int $processNumber
     * @param $proc
     */
    public function setProcesses(int $processNumber, $proc): void
    {
        $this->processes[$processNumber] = $proc;
    }

    /**
     *
     */
    public function parallel(): void
    {
        $process =
            new ParallelProcess(
                new ResourcePool($this->getSettingsList())
            );

        $pool = $process->getResourcePool();
        $pool->configureResourcePoolForParallelProcesses($this);

        $this
            ->startProcessLoop($pool)
            ->closeProcessLoop()
            ->clearResourcePool();
    }

}