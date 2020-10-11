<?php

namespace src\client\planned_routines\single;

use src\client\planned_routines\BaseRoutine;
use src\client\settings\value_object\Settings;
use src\client\settings\value_object\SingleProcessSettings;

class BaseSingleRoutine extends BaseRoutine
{
    protected Settings $settings;

    public function indicateRoutine(array $settings): self
    {
        $this->settings = new SingleProcessSettings($settings);

        return $this;
    }
}