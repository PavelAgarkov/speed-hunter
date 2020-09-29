<?php

namespace src;

use src\process\AsyncProcessManager;
use src\process\ParallelProcessesManager;
use src\process\ProcessManagerInterface;
use src\process\running_process_decorator\MultipleAsyncProcessesDecorator;
use src\process\running_process_decorator\OneAsyncProcessDecorator;
use src\process\running_process_decorator\ParallelProcessesDecorator;
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
        $this->ProcessManager->multiple();

        return $this;
    }

    public function getProcessManager(): ProcessManagerInterface
    {
        return $this->ProcessManager;
    }

    public static function parallel(array $config): Starting
    {
        new ParallelProcessesDecorator(
            $staring =
                new Starting(
                    new ParallelProcessesManager(
                        new ParallelProcessSettings($config)
                    )
                )
        );

        return $staring;
    }

    public static function singleAsyncProcess(array $config): void
    {
        new OneAsyncProcessDecorator(
            $starting =
                (new Starting(
                    new AsyncProcessManager(
                        new SingleProcessSettings($config)
                    )
                ))
        );
    }

    public static function multipleAsyncProcesses($config): void
    {
        new MultipleAsyncProcessesDecorator(
            $starting =
                (new Starting(
                    new AsyncProcessManager(
                        new MultipleAsyncProcessesSettings($config)
                    )
                ))
        );
    }

    public function getOutput(): array
    {
        return $this->getProcessManager()->getOutputData();
    }
}