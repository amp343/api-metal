<?php

namespace ApiMetal\Controller\Parameter;

use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator;

class Parameter
{
    /**
     * The parameter name
     *
     * @var string
     */
    protected $name;

    /**
     * The parameter value
     *
     * @var mixed
     */
    protected $value;

    /**
     * Whether the param is required
     *
     * @var bool
     */
    protected $required;

    /**
     * The Respect validator to use
     *
     * @var Respect\Validator\Validator|null
     */
    protected $validator;

    /**
     * Any custom errors to be used during validation
     *
     * @var array|null
     */
    protected $customErrors;

    /**
     * @param string                            The param name
     * @param mixed|null                        The param value
     * @param bool                              Whether the param is required
     * @param Respect\Validator\Validator|null  The Respect validator to use
     * @param array|null                        Any custom errors to use in validation
     */
    public function __construct(string $name, $value = null, bool $required, Validator $validator = null, array $customErrors = null)
    {
        $this->name = $name;
        $this->value = $value;
        $this->required = $required;
        $this->validator = $validator;
        $this->customErrors = $customErrors ?? [];
    }

    /**
     * Validate the parameter value against its validator
     *
     * @return bool Whether the param is considered valid
     */
    public function validate(): bool
    {
        if (!$this->hasValidator()) {
            return true;
        }

        $validator = $this->isRequired()
            ? $this->getValidator()
            : Validator::optional($this->getValidator());

        return $validator->validate($this->getValue());
    }

    /**
     * Execute the Respect validator's check method
     * against the param value
     */
    public function check()
    {
        return $this->getValidator()->check($this->getValue());
    }

    /**
     * Execute the Respect validator's assert method
     * against the param value
     */
    public function assert()
    {
        return $this->getValidator()->assert($this->getValue());
    }

    /**
     * Return whether the parameter is required
     * but has no value
     *
     * @return bool Whether the parameter is required but has no value
     */
    public function requiredButMissing(): bool
    {
        return $this->getRequired() && $this->getValue() === null;
    }

    /**
     * Return the value of $this->name
     *
     * @return string The value of $this->name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Return the value of $this->value
     *
     * @return mixed|null The value of $this->value
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Return the value of $this->required
     *
     * @return bool The value of $this->required
     */
    public function getRequired(): bool
    {
        return $this->required;
    }

    /**
     * Return the value of $this->validator
     *
     * @return Respect\Validator\Validator|null The value of $this->validator
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * Return the value of $this->customErrors
     *
     * @return array|null The value of $this->customErrors
     */
    public function getCustomErrors()
    {
        return $this->customErrors;
    }

    /**
     * Return the message associated with a failed validation,
     * or false in the case of a successful validation
     *
     * @return string|false The message associated with a failed validation
     */
    public function getValidationError()
    {
        $message = false;

        try {
            $this->assert();
        } catch (ValidationException $e) {
            $customErrors = $e->findMessages($this->getCustomErrors());
            $customError = reset($customErrors);
            $message = $customError ? $customError : $e->getMessages()[0];
            $message = str_replace('"', "'", $message);
            $message = 'Invalid parameter ' . $this->getName() . ': ' . $message;
        } finally {
            return $message;
        }
    }

    /**
     * Return the value of $this->required
     *
     * @return bool The value of $this->required
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * Return whether $this->customErrors is set
     *
     * @return bool Whether $this->customeErrors is set
     */
    public function hasCustomErrors(): bool
    {
        return !empty($this->customErrors);
    }

    /**
     * Return whether $this->validator is set
     *
     * @return bool Whether $this->validator is set
     */
    public function hasValidator(): bool
    {
        return $this->hasValueForProperty('validator');
    }

    /**
     * Return whether $this->value is set
     *
     * @return bool Whether $this->value is set
     */
    public function hasValue(): bool
    {
        return $this->hasValueForProperty('value');
    }

    /**
     * Return whether $this->$property is set
     *
     * @param  string  $property    The property to check
     * @return bool                 Whether $this->property is check
     */
    public function hasValueForProperty(string $property): bool
    {
        return isset($this->$property);
    }

    /**
     * Map an array of Parameters as a [$name => $value] array
     *
     * @param  array  $parameters An array of Parameters
     * @return array              $parameters mapped to a [$name => $value] array
     */
    public static function parameterArrayToKvArray(array $parameters)
    {
        $arr = [];

        foreach ($parameters as $param) {
            $arr[$param->getName()] = $param->getValue();
        }

        return $arr;
    }
}
