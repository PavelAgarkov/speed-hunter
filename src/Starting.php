<?php

namespace src;

use RuntimeException;
use src\process\process_manager\AsyncProcessManager;
use src\process\process_manager\ParallelProcessesManager;
use src\process\process_manager\ProcessManagerInterface;
use src\process\running_process_decorator\MultipleAsyncProcessesDecorator;
use src\process\running_process_decorator\OneAsyncProcessDecorator;
use src\process\running_process_decorator\ParallelProcessesDecorator;
use src\settings\SettingsList;

/**
 * Class Starting
 * @package src
 */
class Starting
{
    /**
     * @var ProcessManagerInterface
     */
    private ProcessManagerInterface $ProcessManager;

    /**
     * Starting constructor.
     * @param ProcessManagerInterface $manager
     */
    private function __construct(ProcessManagerInterface $manager)
    {
        $this->ProcessManager = $manager;
    }

    /**
     * @return $this
     */
    public function parallelRun(): Starting
    {
        $this->ProcessManager->parallel();

        return $this;
    }

    /**
     * @return $this
     */
    public function oneAsyncProcessRun(): Starting
    {
        $this->ProcessManager->single();

        return $this;
    }

    /**
     * @return $this
     */
    public function multipleAsyncProcessesRun(): Starting
    {
        $this->ProcessManager->multiple();

        return $this;
    }

    /**
     * @return ProcessManagerInterface
     */
    public function getProcessManager(): ProcessManagerInterface
    {
        return $this->ProcessManager;
    }

    /**
     * @param SettingsList $settingsList
     * @return Starting
     */
    public static function parallel(SettingsList $settingsList): Starting
    {
        new ParallelProcessesDecorator(
            $staring =
                new Starting(
                    new ParallelProcessesManager($settingsList)
                )
        );

        return $staring;
    }

    /**
     * @param SettingsList $settingsList
     */
    public static function singleAsyncProcess(SettingsList $settingsList): void
    {
        if ($settingsList->getCount() > 1) {
            throw new RuntimeException("SingleProcess can't start some times");
        }

        new OneAsyncProcessDecorator(
            new Starting(
                new AsyncProcessManager($settingsList)
            )
        );
    }

    /**
     * @param SettingsList $settingsList
     */
    public static function multipleAsyncProcesses(SettingsList $settingsList): void
    {
        new MultipleAsyncProcessesDecorator(
            new Starting(
                new AsyncProcessManager($settingsList)
            )
        );
    }

    /**
     * @return array
     */
    public function getOutput(): array
    {
        return $this->getProcessManager()->getOutputData();
    }

    public static function weighData(array $data): int
    {
        return (int)strlen(serialize($data));
    }
}