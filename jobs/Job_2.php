<?php

namespace jobs;

use src\routine\php_routine\RoutineInterface;
use src\routine\php_routine\RoutineTrait;

class Job_2 implements RoutineInterface
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
        $ch = curl_init("https://api.github.com/repos/PavelAgarkov/speed-hunter/events");
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.119 Safari/537.36');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $cont = curl_exec($ch);
        curl_close($ch);

        $array = json_decode($cont, true);
        return $array;
    }
}