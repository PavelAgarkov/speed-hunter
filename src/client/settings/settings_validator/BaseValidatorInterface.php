<?php

namespace src\client\settings\settings_validator;

/**
 * Interface BaseValidatorInterface
 * @package src\client\settings\settings_validator
 */
interface BaseValidatorInterface
{
    public function validate(): void;
}