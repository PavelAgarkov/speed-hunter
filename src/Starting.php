<?php

namespace src;

use src\process\AsyncProcessManager;
use src\process\ParallelProcessesManager;
use src\process\ProcessManagerInterface;
use src\settings\MultipleAsyncProcessesSettings;
use src\settings\ParallelProcessSettings;
use src\settings\SingleProcessSettings;

class Starting
{
    private ProcessManagerInterface $ProcessManager;

    private function __construct(ProcessManagerInterface $manager)
    {
        $this->ProcessManager = $manager;
    }

    public function parallelRun(): Starting
    {
        $this->ProcessManager->parallel();
        return $this;
    }

    public function oneAsyncProcessRun(): Starting
    {
        $this->ProcessManager->single();
        return $this;
    }

    public function multipleAsyncProcessesRun(): Starting
    {
        return $this;
    }

    public function getProcessManager(): ProcessManagerInterface
    {
        return $this->ProcessManager;
    }

    public static function startingParallel(array $config): Starting
    {
        return (new Starting(
            new ParallelProcessesManager(
                new ParallelProcessSettings(
                    $config
                )
            )
        ))->parallelRun();
    }

    public static function startingOneAsyncProcess(array $config): Starting
    {
        return (new Starting(
            new AsyncProcessManager(
                new SingleProcessSettings(
                    $config
                )
            )
        ))->oneAsyncProcessRun();
    }

    public static function startingMultipleAsyncProcesses($config): Starting
    {
        return (new Starting(
            new AsyncProcessManager(
                new MultipleAsyncProcessesSettings(
                    $config
                )
            )
        ))->multipleAsyncProcessesRun();
    }

    public function getOutput(): array
    {
        return $this->getProcessManager()->getOutputData();
    }

}