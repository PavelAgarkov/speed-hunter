<?php

namespace src\process;

use src\data_manager\DataManagerForWorkers;
use src\data_manager\DataPartitioningStrategy;
use src\data_manager\PutDataInJobSharedMemoryStrategy;
use src\settings\ParallelProcessSettings;
use src\SharedMemory;
use src\process\WorkerProcess;

/** Класс для управления параллельными php процессами взаимодействующими через разделяемую память unix.
 * Class ProcessesManager
 * @package src
 */
class ParallelProcessesManager
{
    /**
     * @var array - записи о каналах связи.
     */
    private array $pipes = [];

    /**
     * @var array - запущенные процессы.
     */
    public array $processes = [];

    /**
     * @var array - указатели на каналы связи между процессами.
     */
    private array $processPipes = [];

    /**
     * @var array - набор воркеров
     */
    private array $poolOfWorkers;

    /**
     * @var array - массив объектов DataManager для каждого набора WorkerProcess
     */
    private array $dataManagerForWorkers;

    /**
     * @var \src\SharedMemory - объект разделяемой памяти
     */
    private SharedMemory $SharedMemory;

    private ParallelProcessSettings $settings;

    /** Метод для открытия нового процесса php передающего в открытый процесс данные о номере процесса
     *  относительно родительского, а так же данные для заполнения разделяемой памяти из созданного процесса.
     * @param string $workerName - имя воркера.
     * @param int $processNumber - порядковый номер запускаемого процесса для родительского.
     * @param int $numberMemoryKey - ключ разделяемой памяти, по которому внутри воркера открывается
     *                              соединения для записи в участок разделяемой памяти.
     * @param array $descriptors - дискрипторы для настройки каналов.
     * @param int $memorySize - размер памяти для восстановления подключения к ресурсу разделяемой
     *                          памяти внутри воркера.
     */
    public function openProcess(
        string $workerName,
        int $processNumber,
        int $numberMemoryKey,
        array $descriptors,
        int $memorySize
    ): void
    {
        $unserializeFlag = 0;
        if (array_key_exists($workerName, $this->dataManagerForWorkers)) {
            $unserializeFlag = 1;
        } else $unserializeFlag = 0;

        $proc = proc_open(
            "php {$workerName}.php {$processNumber} {$numberMemoryKey} {$memorySize} {$unserializeFlag}",
            $descriptors,
            $this->processPipes);
        $this->processes[$processNumber] = $proc;
        $this->pipes[$processNumber] = $this->processPipes;
    }

    /** Метод открывает цикл процессов, который передает управление воркерам.
     *  По окончанию выполнения последнего воркера цикл возвращает управление основному процессу.
     * @return ParallelProcessesManager
     */
    public function startProcessLoop(): ParallelProcessesManager
    {
        foreach ($this->SharedMemory->getResourcePool() as $workerName => $configurations) {
            foreach ($configurations as $resourceKey => $value) {
                $numberMemoryKey = $value[1];

                $process = new ParallelProcess();
                $process->processOpen(
                    $workerName,
                    $resourceKey,
                    $numberMemoryKey,
                    [
                        0 => ['pipe', 'r'],
                        1 => ['pipe', 'w'],
                    ],
                    $this->poolOfWorkers[$workerName]->getMemorySize(),
                    $this
                );
            }
        }
        // демонстрация каналов для отладки

//        while (array_filter($this->processes, function ($proc) {
//            return proc_get_status($proc)['running'];
//        })) {
//            foreach (range(0, 4) as $i) {
////        usleep(10 * 1000); // 100ms
//                // Read all available output (unread output is buffered).
//
//                $str = fread($this->pipes[$i][1], 50);
//
//                if ($str) {
//                    printf($str);
//                }
//            }
//        }
////
        return $this;
    }

    /** Метод закрывающий каналы и процессы, открытые для работы.
     * @return ParallelProcessesManager
     */
    public function closeProcessLoop(): ParallelProcessesManager
    {
        foreach ($this->SharedMemory->getResourcePool() as $workerName => $configurations) {
            foreach ($configurations as $resourceKey => $value) {
                fclose($this->pipes[$resourceKey][1]);
                proc_close($this->processes[$resourceKey]);
            }
        }

        $this->SharedMemory->readAllDataFromResourcePool();

        return $this;
    }

    /** Метод приримает массив конфигураций, создает менеджера управления разделяемой памятью
     *  для каждого набора воркеров, создает пул ресурсов разделяемой памяти, заполняет ресурс
     *  для каждого набора воркеров разбитыми данными на воркеры.
     * @param ParallelProcessSettings $settings
     * @return $this
     * @throws \Exception
     */
    public function configureProcessesLoop(ParallelProcessSettings $settings): ParallelProcessesManager
    {
        $this->settings = $settings;

        // 1 block
        $SharedMemory = new SharedMemory();
        $this->SharedMemory = $SharedMemory;

        // 2 block
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

                $DataManager = $this->dataManagerForWorkers[$jobSettings["jobName"]] =
                    new DataManagerForWorkers(
                        $poolOfWorkers[$jobSettings["jobName"]],
                        $jobSettings["dataPartitioning"]
                    );

                $strategy = new DataPartitioningStrategy($DataManager);
                $strategy->prepareDataForRecording();
            }
        }

        $this->poolOfWorkers = &$poolOfWorkers;

        // 3 block
        $this->SharedMemory->createResourcePool($this->poolOfWorkers);

        // 4 block
        foreach ($this->settings->getSettingsObjects() as $key => $configuration) {

            $jobSettings = $configuration->getJobTypeSettings();
            if (isset($jobSettings["dataPartitioning"])) {

                $strategy = new PutDataInJobSharedMemoryStrategy(
                    $this->dataManagerForWorkers[$jobSettings["jobName"]],
                    $this->SharedMemory
                );
                $strategy->putData();
            }
        }

        return $this;
    }

    /** Метод очищает пул ресурсов от данных из воркеров
     * @return bool
     */
    public function clearResourcePool(): bool
    {
        return $this->SharedMemory->deleteAllDataFromResourcePool();
    }

    /** Метод управляет получение выходных данных из разделяемой памяти по ключу(или всех).
     * @param string|null $workerName - имя воркера
     * @return array
     */
    public function getOutputData(string $workerName = null): array
    {
        return $this->SharedMemory->getData($workerName);
    }

    /** Метод возвращает пул ресурсво из объекта разделяемой памяти
     * @return array
     */
    public function getResourceMemoryData(): array
    {
        return $this->SharedMemory->getResourcePool();
    }

    public function &getProcessPipes(): array
    {
        return $this->processPipes;
    }

    public function getDataManagerForWorkers(): array
    {
        return $this->dataManagerForWorkers;
    }

    public function getPipes(): array
    {
        return $this->pipes;
    }

}