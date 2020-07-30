<?php

namespace src;

class Processes
{
    private $pipes = [];

    public $processes = [];

    private $processPipes = [];

    public function openProcess(string $workerName, int $processNumber, int $numberMemoryKey, array $descriptors): void
    {
        $proc = proc_open("php {$workerName} {$processNumber} {$numberMemoryKey}", $descriptors, $this->processPipes);
        $this->processes[$processNumber] = $proc;
        $this->pipes[$processNumber] = $this->processPipes;
    }

    public function startProcessLoop(int $countResources, array $resourcePool, string $workerName): void
    {
        foreach (range(0, $countResources - 1) as $processKey => $item) {

            $numberMemoryKey = current($resourcePool[$processKey])[1];

            $this->openProcess(
                $workerName,
                $processKey,
                $numberMemoryKey,
                [
                    0 => ['pipe', 'r'],
                    1 => ['pipe', 'w'],
                ],
            );
        }
    }

    public function closePipesAndProcesses(int $countResources): void
    {
        foreach (range(0, $countResources - 1) as $processKey => $item) {
            fclose($this->pipes[$processKey][1]);
            proc_close($this->processes[$processKey]);
        }
    }

}