<?php

namespace src\process;

/** Класс для создания сета однотипных процессов
 * Class WorkerProcess
 * @package src
 */
class WorkerProcess
{
    /**
     * @var string
     */
    private string $phpPath;

    /**
     * @var mixed|string - имя воркера
     */
    private string $workerName;

    /**
     * @var int|mixed - количетво воркеров в сете
     */
    private int $countWorkers;

    /**
     * @var int|mixed - размер разделяемой памяти для каждого варкера из набора
     */
    private int $memorySize;

    /**
     * WorkerProcess constructor.
     * @param array $workerSettings
     */
    public function __construct(array $workerSettings)
    {
        $this->phpPath = (isset($workerSettings["phpPath"]) && $workerSettings["phpPath"] !== null)
            ? $workerSettings["phpPath"]
            : 'php';

        $this->workerName = $workerSettings["jobName"];

        $this->countWorkers = isset($workerSettings["numberJobs"])
            ? $workerSettings["numberJobs"]
            : 1;

        $this->memorySize = $workerSettings["shSizeForOneJob"];
    }

    /** Метод возвращает количество воркеров
     * @return int
     */
    public function getCountWorkers(): int
    {
        return $this->countWorkers;
    }

    /** Метод возвращает размер разделяемой памяти дял каждого воркера из сета
     * @return int
     */
    public function getMemorySize(): int
    {
        return $this->memorySize;
    }

    /** Метод возвращает имя файла воркера для набора
     * @return string
     */
    public function getWorkerName(): string
    {
        return $this->workerName;
    }

    /**
     * @return string
     */
    public function getPhpPath(): string
    {
        return $this->phpPath;
    }
}