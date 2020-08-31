<?php

namespace src;

use src\process\ProcessManagerInterface;

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
        return (new \src\Starting(
            new \src\process\ParallelProcessesManager(
                new \src\settings\ParallelProcessSettings(
                    $config
                )
            )
        ))->parallelRun();
    }

    public static function startingOneAsyncProcess(array $config): Starting
    {
        return (new \src\Starting(
            new \src\process\AsyncProcessManager(
                new \src\settings\SingleProcessSettings(
                    $config
                )
            )
        ))->oneAsyncProcessRun();
    }

    public static function startingMultipleAsyncProcesses($config): Starting
    {
        return (new \src\Starting(
            new \src\process\AsyncProcessManager(
                new \src\settings\MultipleAsyncProcessesSettings(
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