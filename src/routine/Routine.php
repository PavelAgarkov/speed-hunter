<?php

namespace src\routine;

use src\job\Job;

/**
 * Class Routine
 * @package src\routine
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

}