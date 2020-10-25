<?php

namespace jobs;

use src\routine\php_routine\RoutineInterface;
use src\routine\php_routine\RoutineTrait;

class Async_2 implements RoutineInterface
{
    use RoutineTrait;

    public function execute(): self
    {
        $Job = $this->Routine->getJob();
        $Job->synchroniseRead();
        $read = $Job->getOutput();

        $id = posix_getpid();
        $fp = fopen("t{$id}.txt", "w");
        $str = $id;
        fwrite($fp, " {$str} \r\n");
        fclose($fp);

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