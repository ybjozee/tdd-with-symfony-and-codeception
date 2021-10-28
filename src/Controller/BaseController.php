<?php

namespace App\Controller;

use App\Exception\InvalidParameterException;
use App\Exception\ParameterNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class BaseController extends AbstractController {

    /**
     * @throws ParameterNotFoundException|InvalidParameterException
     */
    protected function getRequiredNonNegativeNumber(string $parameterName, array $requestBody) {

        $formattedParameterName = ucfirst($parameterName);
        $requiredParameter = $this->getRequiredParameter(
            $parameterName,
            $requestBody,
            "$formattedParameterName is required"
        );
        if (!is_numeric($requiredParameter)) {
            throw new InvalidParameterException("$formattedParameterName must be a number");
        }
        if ($requiredParameter < 0) {
            throw new InvalidParameterException("$formattedParameterName cannot be negative");
        }

        return $requiredParameter;
    }

    protected function getRequiredParameter(
        string $parameterName,
        array  $requestBody,
        string $errorMessage
    ) {

        if (!isset($requestBody[$parameterName])) {
            throw new ParameterNotFoundException($errorMessage);
        }

        return $requestBody[$parameterName];
    }
}