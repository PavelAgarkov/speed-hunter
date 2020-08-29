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
        $this->ProcessManager
            ->configureProcessesLoop()
            ->startProcessLoop()
            ->closeProcessLoop()
            ->clearResourcePool();
        return $this;
    }

    public function oneAsyncProcessRun(): Starting
    {
        $this->ProcessManager->single();
        return $this;
    }

    public function multipleAsyncProcessesRun(): void
    {

    }

    public function getProcessManager(): ProcessManagerInterface
    {
        return $this->ProcessManager;
    }

    public static function startingParallel(array $config): Starting
    {
        return new \src\Starting(
            new \src\process\ParallelProcessesManager(
                new \src\settings\ParallelProcessSettings(
                    $config
                )
            )
        );
    }

    public static function startingOneAsyncProcess(array $config): Starting
    {
        return new \src\Starting(
            new \src\process\AsyncProcessManager(
                new \src\settings\SingleProcessSettings(
                    $config
                )
            )
        );
    }

    public static function startingMultipleAsyncProcesses() : Starting
    {

    }

}