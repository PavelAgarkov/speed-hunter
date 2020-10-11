<?php

namespace src\client\planned_routines\multiple;

use src\client\planned_routines\BaseRoutine;
use src\client\settings\SettingsList;
use src\client\settings\value_object\MultipleProcessesSettings;
use src\client\settings\value_object\Settings;

class BaseMultimpleRoutine extends BaseRoutine
{
    protected SettingsList $settingsList;

    protected Settings $settings;

    public function __construct()
    {
        $this->settingsList = new SettingsList();
    }

    public function addRoutine(... $settings): self
    {
        $processSettings =  new MultipleProcessesSettings($settings[0]);

        if(!isset($this->settings)) {
            $this->settings = $processSettings;
        }

        $this
            ->settingsList
            ->set(
                $processSettings->getJobName(),
                $processSettings
            );

        return $this;
    }
}