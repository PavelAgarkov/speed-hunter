<?php

namespace src\routine\job\value_object;

/**
 * Class LaunchedJob
 * @package src\job\value_object
 */
class LaunchedJob
{
    /**
     * @var string - имя зпущенного воркера
     */
    private string $jobName;

    /**
     * @var int - номер процесса по счету, переданный при запуске процесса, вызванного из основного процесса
     */
    private int $processNumber;

    /**
     * @var int
     */
    private int $serializeFlag;

    /**
     * LaunchedJob constructor.
     * @param array $inputData
     */
    public function __construct(array $inputData)
    {
        $this->jobName = $inputData["jobName"];
        $this->processNumber = $inputData["processNumber"];
        $this->serializeFlag = $inputData["serializeFlag"];
    }

    /**
     * @return string
     */
    public function getJobName(): string
    {
        return $this->jobName;
    }

    /**
     * @return int
     */
    public function getProcessNumber(): int
    {
        return $this->processNumber;
    }

    /**
     * @return int
     */
    public function getSerializeFlag(): int
    {
        return $this->serializeFlag;
    }
}