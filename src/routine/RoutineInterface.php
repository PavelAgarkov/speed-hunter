<?php

namespace src\routine;

/**
 * Interface RoutineInterface
 * @package src\routine
 */
interface RoutineInterface
{
    public function beforeExecute(): self;

    public function execute(): self;

    public function afterExecute(): self;

    public function setRoutine(Routine $Routine);
}

