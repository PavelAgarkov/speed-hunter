<?php

namespace src\client\settings\settings_validator;

interface BaseValidatorInterface
{
    public const BASE_VALIDATE_VALUE = [
      "phpPath" => [
          "type"        => "string",
          "required"    => "required",
      ],

      "jobName" => [
          "type"        => "string",
          "required"    => "required",
      ]
    ];

}