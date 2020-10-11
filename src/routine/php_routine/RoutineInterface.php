<?php

namespace src\routine\php_routine;

/**
 * Interface RoutineInterface
 * @package src\php_routine
 */
interface RoutineInterface
{
    public function beforeExecute(): self;

    public function execute(): self;

    public function afterExecute(): self;

    public function setRoutine(Routine $Routine);

    public function getRoutine(): Routine;
}

