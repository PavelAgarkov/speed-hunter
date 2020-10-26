<?php

namespace src\client\process\value_object;

use src\client\settings\value_object\Settings;

/** Класс для создания сета однотипных процессов
 * Class WorkerProcess
 * @package src
 */
final class WorkerProcess
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
     * @param Settings $settings
     */
    public function __construct(Settings $settings)
    {
        $this->phpPath = ($settings->getPhpPath() and $settings->getPhpPath() !== null)
            ? $settings->getPhpPath()
            : 'php';

        $this->workerName = $settings->getJobName();
        $this->countWorkers = $settings->getNumberJobs();
        $this->memorySize = $settings->getShSizeForOneJob();
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