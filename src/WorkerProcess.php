<?php

namespace src;

class WorkerProcess
{
    private string $workerName;

    private int $countWorkers;

    private int $memorySize;

    public function __construct(array $workerSettings) {
        $this->workerName = $workerSettings[0];
        $this->countWorkers = $workerSettings[1];
        $this->memorySize = $workerSettings[2];
    }

    public function getCountWorkers() : int
    {
        return $this->countWorkers;
    }

    public function getMemorySize() : int
    {
        return $this->memorySize;
    }

    public function getWorkerName() : string
    {
        return $this->workerName;
    }

}