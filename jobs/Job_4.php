<?php

namespace jobs;

use src\routine\php_routine\RoutineInterface;
use src\routine\php_routine\RoutineTrait;

class Job_4 implements RoutineInterface
{
    use RoutineTrait;

    public function execute(): self
    {
        $Job = $this->Routine->getJob();
        $Job->insertInOutput("fromJob", $this->logic());
        $Job->runJob();

        return $this;
    }

    public function logic(): array
    {
        $array = [10];
        foreach (range(0, 9) as $key => $value) {
            $array[] = $value;
        }
        return $array;
    }
}