<?php

namespace src\routine;

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