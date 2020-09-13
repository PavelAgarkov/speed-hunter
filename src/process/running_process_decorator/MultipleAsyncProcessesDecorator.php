<?php

namespace src\process\running_process_decorator;

use src\Starting;

class MultipleAsyncProcessesDecorator extends Decorator
{
    public function __construct(Starting $starting)
    {
        parent::__construct($starting);
        $this->execute();
    }

    public function execute(): void
    {
        $this->starting->multipleAsyncProcessesRun();
    }
}