<?php

namespace src\routine\php_routine;

use src\routine\job\Job;

/**
 * Class Routine
 * @package src\php_routine
 */
class Routine
{
    /**
     * @var Job
     */
    private Job $Job;

    /**
     * Routine constructor.
     * @param array $argv
     */
    public function __construct(array $argv)
    {
        $this->Job = new Job($argv);
    }

    /**
     * @return Job
     */
    public function getJob(): Job
    {
        return $this->Job;
    }

    public function clear(): void
    {
        if($this->Job->getSharedMemoryJob() !== null and
            ($this->Job->isSingleAsync() or $this->Job->isMultipleAsync()))
        {
            $this->Job->runSingleAsyncJob();
        }
    }

}