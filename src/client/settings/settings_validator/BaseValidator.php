<?php

namespace src\client\settings\settings_validator;

use src\client\settings\value_object\Settings;

abstract class BaseValidator
{
    /**
     * @var Settings
     */
    protected Settings $settings;

    /**
     * @var array|string[][]
     */
    protected array $commonRights =
        [
            "phpPath" => [
                "type"        => "string",
                "required"    => "required",
            ],

            "jobName" => [
                "type"        => "string",
                "required"    => "required",
            ]
        ];

    /**
     * BaseValidator constructor.
     * @param Settings $settings
     */
    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @param array $rights
     */
    public function baseValidate(array $rights): void
    {
        $this->recursion($this->commonRights);
        $this->recursion($rights);
    }

    /**
     * @param array $rights
     */
    protected function recursion(array $rights)
    {
        foreach ($rights as $key => $right) {
            $name = "get".ucfirst($key);
            if(!is_numeric($name)) {
                $methodValue = $this->settings->$name();
            }

            if((is_array($methodValue) or $methodValue === []) and isset($methodValue["flagPartitioning"])) {
                $this->recursion($right);
            } else {
                $equals = $this->nodeEquals($methodValue, $right);
                if(!$equals) {
                    throw new \RuntimeException("Параметр {$key} указан не верно или отсутствует");
                }
            }
        }
    }

    /**
     * @param $methodValue
     * @param array $right
     * @return bool
     */
    protected function nodeEquals($methodValue, array $right): bool
    {
        if(gettype($methodValue) !== $right["type"]) {
            return false;
        }

        if(empty($methodValue) and $right["required"] === "required") {
            return false;
        }

        if(isset($right["depend"])) {
            $name = "get".ucfirst($right["depend"]);
            $methodValue = $this->settings->$name();

            if($methodValue === null) {
                return false;
            }
        }

        return true;
    }
}