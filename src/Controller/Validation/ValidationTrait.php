<?php

namespace ApiMetal\Controller\Validation;

use ApiMetal\Controller\Parameter\Parameter;

/**
 * ValidationTrait is a module of validation functions
 * that are valid in the context of a ApiMetal\Controller\MetalController
 */
trait ValidationTrait
{
    /**
     * Given an array of parameters in the simplified
     * MetalController dsl format; that is, an array like:
     * [
     *     ['name' =>, 'value' =>, 'required' =>, 'default' =>],
     *     ['name' =>, 'value' =>, 'required' =>, 'default' =>],
     *     ...
     * ]
     * ... return an array of Parameter instances
     * constructed from that simplified format
     *
     * @param  array  $params An array of parameters in the simplified
     *                        MetalController dsl format
     * @return [type]         An array of Parameter instances constructed
     *                        from that format
     */
    public function mapParameters(array $params): array
    {
        return array_map(
            function ($name, $rules) {
                $type = array_key_exists('type', $rules)
                    ? $rules['type']
                    : null;

                $matchedValidator = ValidatorFactory::getValidatorByType($type);

                return new Parameter(
                    $name,
                    $this->getParam($name) ?? $rules['default'] ?? null,
                    $rules['required'],
                    $matchedValidator['validator'],
                    $matchedValidator['errors']
                );
            },
            array_keys($params), $params
        );
    }

    /**
     * Validate allowed params; that is, check whether
     * there are any $sentParams that are not present
     * in $allowedParams
     *
     * @param  array  $allowedParams An exhaustive list of allowed parameters
     * @param  array  $sentParams    A list of sent parameters
     *
     * @throws ApiMetal\Error\Error  An error describing failed validation
     * @return bool                     Whether the validation passed
     */
    public function validateAllowedParams(array $allowedParams, array $sentParams): bool
    {
        $illegalParams = array_values(array_diff($sentParams, $allowedParams));

        if (!empty($illegalParams)) {
            $message = 'The ' . htmlspecialchars($illegalParams[0]) . ' parameter is not permitted';
            throw new static::$paramValidationErrorClass($message);
        }

        return true;
    }

    /**
     * Validate required params; that is, check that
     * all required params also have at least some $value
     *
     * @param  array  $params An array of parameters whose status to check
     *
     * @throws ApiMetal\Error\Error  An error describing failed validation
     * @return bool                     Whether the validation passed
     */
    public function validateRequiredParams(array $params)
    {
        array_map(
            function (Parameter $param) {
                if ($param->requiredButMissing()) {
                    throw new static::$paramValidationErrorClass("The {$param->getName()} parameter is required.");
                }
            },
            $params
        );

        return true;
    }

    /**
     * Validated param values; that is, validate
     * their $values against their $validators
     *
     * @param  array  $params An array of Parameters to validate
     *
     * @throws ApiMetal\Error\Error  An error describing failed validation
     * @return bool                     Whether the validation passed
     */
    public function validateParamValues(array $params)
    {
        array_map(
            function ($param) {
                return $param->hasValue()
                    ? $this->validateParamValue($param)
                    : true;
            },
            $params
        );

        return true;
    }

    /**
     * Validated a Parameter' values; that is,
     * validate its $value against its $validator
     *
     * @param  ApiMetal\Controller\Parameter\Parameter  $param A Parameter
     *                                                            to validate
     *
     * @throws ApiMetal\Error\Error  An error describing failed validation
     * @return bool                     Whether the validation passed
     */
    public function validateParamValue(Parameter $param)
    {
        if ($param->validate()) {
            return true;
        }

        // else, there's an error
        $message = $param->getValidationError();
        throw new static::$paramValidationErrorClass($message);
    }

    /**
     * Validate an array of MetalController-style dsl parameters; that is:
     * - validate allowed parameters
     * - validate required parameters
     * - validate parameter values
     * ... and update
     *
     * @param  array  $params         An array of MetalController-style dsl
     *                                parameters to validate
     * @param  bool   $updateDefaults Whether default Request parameters values
     *                                should be updated to the default values
     *                                specified in $params if validation passes
     */
    public function validate(array $params, bool $rejectUnknownParams = true, bool $updateDefaults = true)
    {
        if ($rejectUnknownParams) {
            // check for illegal params
            $allowedParams = array_keys($params);
            $sentParams = array_keys($this->getRequest()->getParams());
            $this->validateAllowedParams($allowedParams, $sentParams);
        }

        // turn the simple controller validation dsl
        // into an array of Parameters
        $parameters = $this->mapParameters($params);

        // check for required params
        $this->validateRequiredParams($parameters);

        // check for param values
        $this->validateParamValues($parameters);

        // update $this->params with default values from $params
        // if $updateDefaults is true
        if ($updateDefaults) {
            ($this->getRequest())->setParams(
                Parameter::parameterArrayToKvArray($parameters),
                ($this->getRequest())->getParams()
            );
        }

        $this->setParamsValidated(true);
    }
}
