<?php

namespace src\routine\php_routine;

use src\routine\php_routine\Routine;

trait RoutineTrait
{
    /**
     * @var Routine
     */
    public Routine $Routine;

    /**
     * @param Routine $routine
     * @return RoutineTrait
     */
    public function setRoutine(Routine $routine): self
    {
        $this->Routine = $routine;

        return $this;
    }

    /**
     * @return Routine
     */
    public function getRoutine(): Routine
    {
        return $this->Routine;
    }
}