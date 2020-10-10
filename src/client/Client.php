<?php

namespace src\client;

use RuntimeException;
use src\client\process\services\AsyncProcessService;
use src\client\process\services\ParallelProcessesService;
use src\client\process\services\ProcessServiceInterface;
use src\client\process\decorators\MultipleAsyncProcessesDecorator;
use src\client\process\decorators\OneAsyncProcessDecorator;
use src\client\process\decorators\ParallelProcessesDecorator;
use src\client\settings\SettingsList;

/**
 * Class Client
 * @package src
 */
class Client
{
    /**
     * @var ProcessServiceInterface
     */
    private ProcessServiceInterface $ProcessService;

    /**
     * Starting constructor.
     * @param ProcessServiceInterface $service
     */
    private function __construct(ProcessServiceInterface $service)
    {
        $this->ProcessService = $service;
    }

    /**
     * @return $this
     */
    public function parallelRun(): self
    {
        $this->ProcessService->parallel();

        return $this;
    }

    /**
     * @return $this
     */
    public function oneAsyncProcessRun(): self
    {
        $this->ProcessService->single();

        return $this;
    }

    /**
     * @return $this
     */
    public function multipleAsyncProcessesRun(): self
    {
        $this->ProcessService->multiple();

        return $this;
    }

    /**
     * @param SettingsList $settingsList
     * @return static
     */
    public static function parallel(SettingsList $settingsList): self
    {
        new ParallelProcessesDecorator(
            $Client =
                new self(
                    new ParallelProcessesService($settingsList)
                )
        );

        return $Client;
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
            new self(
                new AsyncProcessService($settingsList)
            )
        );
    }

    /**
     * @param SettingsList $settingsList
     */
    public static function multipleAsyncProcesses(SettingsList $settingsList): void
    {
        new MultipleAsyncProcessesDecorator(
            new self(
                new AsyncProcessService($settingsList)
            )
        );
    }

    /**
     * @return array
     */
    public function getOutput(): array
    {
        return $this->ProcessService->getOutputData();
    }

    public static function weighData(array $data): int
    {
        return (int)strlen(serialize($data));
    }
}