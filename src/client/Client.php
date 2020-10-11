<?php

namespace src\client;

use src\client\planned_routines\multiple\AsyncRoutine;
use src\client\planned_routines\multiple\ParallelRoutine;
use src\client\planned_routines\single\AsyncSingleRoutine;
use src\client\planned_routines\single\SingleRoutine;
use src\client\process\services\ProcessServiceInterface;

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
    public function __construct(ProcessServiceInterface $service)
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
    public function oneProcessRun(): self
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
     * @return $this
     */
    public function oneAsyncProcessRun(): self
    {
        $this->ProcessService->single();

        return $this;
    }

    /**
     * @return array
     */
    public function getOutput(): array
    {
        return $this->ProcessService->getOutputData();
    }

    /**
     * @param array $data
     * @return int
     */
    public static function weighData(array $data): int
    {
        return (int)strlen(serialize($data));
    }

    /**
     * @return ParallelRoutine
     */
    public static function getParallelRoutine(): ParallelRoutine
    {
        return new ParallelRoutine();
    }

    /**
     * @return AsyncRoutine
     */
    public static function getAsyncRoutine(): AsyncRoutine
    {
        return new AsyncRoutine();
    }

    /**
     * @return AsyncSingleRoutine
     */
    public static function getSingleAsyncRoutine(): AsyncSingleRoutine
    {
        return new AsyncSingleRoutine();
    }

    /**
     * @return SingleRoutine
     */
    public static function getSingleRoutine(): SingleRoutine
    {
        return new SingleRoutine();
    }
}