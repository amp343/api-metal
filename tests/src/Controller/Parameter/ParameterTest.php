<?php

namespace ApiMetal\Tests\Controller\Validation;

use ApiMetal\Controller\Parameter\Parameter;
use Respect\Validation\Validator as v;

class ParameterTest extends \ApiMetal\Tests\TestCase
{
    /**
     * @test
     */
    public function construct_should_throw_TypeError_if_name_is_not_a_string()
    {
        $this->assertTypeError(function () {
            return new Parameter;
        });
    }

    /**
     * @test
     */
    public function construct_should_throw_TypeError_if_required_is_not_bool()
    {
        $this->assertTypeError(function () {
            return new Parameter('', '', []);
        });
    }

    /**
     * @test
     */
    public function construct_should_throw_TypeError_if_validator_is_not_Validator()
    {
        $this->assertTypeError(function () {
            return new Parameter('', '', true, []);
        });
    }

    /**
     * @test
     */
    public function construct_should_throw_TypeError_if_customErrors_is_not_array()
    {
        $this->assertTypeError(function () {
            return new Parameter('', '', true, null, '');
        });
    }

    /**
     * @test
     */
    public function getName_should_return_the_value_of_this_name()
    {
        $expected = $this->getValidationParams()['name'];
        $paramValidator = $this->buildParamValidator();

        $this->assertEquals($paramValidator->getName(), $expected);
    }

    /**
     * @test
     */
    public function getValue_should_return_the_value_of_this_value()
    {
        $expected = $this->getValidationParams()['value'];
        $paramValidator = $this->buildParamValidator();

        $this->assertEquals($paramValidator->getValue(), $expected);
    }

    /**
     * @test
     */
    public function getRequired_should_return_the_value_of_this_required()
    {
        $expected = $this->getValidationParams()['required'];
        $paramValidator = $this->buildParamValidator();

        $this->assertEquals($paramValidator->getRequired(), $expected);
    }

    /**
     * @test
     */
    public function isRequired_should_return_the_value_of_this_required()
    {
        $expected = $this->getValidationParams()['required'];
        $paramValidator = $this->buildParamValidator();

        $this->assertEquals($paramValidator->isRequired(), $expected);
    }

    /**
     * @test
     */
    public function getValidator_should_return_the_value_of_this_validator()
    {
        $paramValidator = $this->buildParamValidator();

        $this->assertInstanceOf('\Respect\Validation\Validator', $paramValidator->getValidator());
    }

    /**
     * @test
     */
    public function getCustomErrors_should_return_the_value_of_this_customErrors()
    {
        $expected = $this->getValidationParams()['customErrors'];
        $paramValidator = $this->buildParamValidator();

        $this->assertEquals($paramValidator->getCustomErrors(), $expected);
    }

    /**
     * @test
     */
    public function hasCustomErrors_should_return_true_when_there_are_customErrors()
    {
        $paramValidator = $this->buildParamValidator();

        $this->assertTrue($paramValidator->hasCustomErrors());
    }

    /**
     * @test
     */
    public function hasCustomErrors_should_return_false_when_there_are_not_customErrors()
    {
        $paramValidator = new Parameter('', '', true, v::intVal());

        $this->assertFalse($paramValidator->hasCustomErrors());
    }

    /**
     * @test
     */
    public function hasValidator_should_return_true_when_there_is_a_validator()
    {
        $paramValidator = $this->buildParamValidator();

        $this->assertTrue($paramValidator->hasValidator());
    }

    /**
     * @test
     */
    public function hasValidator_should_return_false_when_there_is_no_validator()
    {
        $paramValidator = new Parameter('', '', true);

        $this->assertFalse($paramValidator->hasValidator());
    }

    /**
     * @test
     */
    public function hasValue_should_return_true_when_there_is_a_value()
    {
        $paramValidator = $this->buildParamValidator();

        $this->assertTrue($paramValidator->hasValue());
    }

    /**
     * @test
     */
    public function hasValue_should_return_false_when_there_is_no_value()
    {
        $paramValidator = new Parameter('', null, true);

        $this->assertFalse($paramValidator->hasValue());
    }

    /**
     * @test
     */
    public function requiredButMissing_should_return_true_if_required_is_true_but_there_is_no_value()
    {
        $paramValidator = new Parameter('', null, true);

        $this->assertTrue($paramValidator->requiredButMissing());
    }

    /**
     * @test
     */
    public function requiredButMissing_should_return_false_if_required_is_false_and_value_exists()
    {
        $paramValidator = new Parameter('', 123, false);

        $this->assertFalse($paramValidator->requiredButMissing());
    }

    /**
     * @test
     */
    public function requiredButMissing_should_return_false_if_required_is_true_and_value_exists()
    {
        $paramValidator = new Parameter('', 123, true);

        $this->assertFalse($paramValidator->requiredButMissing());
    }

    /**
     * @test
     */
    public function getValidationError_should_return_false_if_there_is_no_validationError()
    {
        $paramValidator = $this->buildParamValidator();

        $this->assertFalse($paramValidator->getValidationError());
    }

    /**
     * @test
     */
    public function getValidationError_should_return_a_string_if_there_is_a_validationError()
    {
        $paramValidator = new Parameter('', [], true, v::intVal());

        $this->assertTrue(is_string($paramValidator->getValidationError()));
    }

    /**
     * @test
     */
    public function getValidationError_should_return_the_default_validation_error_if_there_are_no_customErrors()
    {
        $paramValidator = new Parameter('someParam', 40.55, true, v::intVal());
        $expected = 'Invalid parameter someParam: 40.55 must be an integer number';

        $this->assertEquals($paramValidator->getValidationError(), $expected);
    }

    /**
     * @test
     */
    public function getValidationError_should_return_the_default_validation_error_if_there_are_customErrors_but_no_matches()
    {
        $paramValidator = new Parameter('someParam', 40.55, true, v::intVal(), ['positive' => '{{name}} must be legit']);
        $expected = 'Invalid parameter someParam: 40.55 must be an integer number';

        $this->assertEquals($paramValidator->getValidationError(), $expected);
    }

    /**
     * @test
     */
    public function getValidationError_should_return_the_matching_customError_if_there_are_matching_customErrors()
    {
        $paramValidator = new Parameter('someParam', 40.55, true, v::intVal(), ['intVal' => '{{name}} must be legit']);
        $expected = 'Invalid parameter someParam: 40.55 must be legit';

        $this->assertEquals($paramValidator->getValidationError(), $expected);
    }

    /**
     * @test
     */
    public function validate_should_return_true_if_the_validator_property_is_not_set()
    {
        $paramValidator = new Parameter('someParam', 40.55, true);

        $this->assertTrue($paramValidator->validate());
    }

    /**
     * @test
     */
    public function validate_should_return_true_if_the_validator_property_is_set_and_value_is_valid()
    {
        $paramValidator = new Parameter('someParam', 123, true, v::intVal());

        $this->assertTrue($paramValidator->validate());
    }

    /**
     * @test
     */
    public function validate_should_return_false_if_the_validator_property_is_set_and_value_is_not_valid()
    {
        $paramValidator = new Parameter('someParam', 40.55, true, v::intVal());

        $this->assertFalse($paramValidator->validate());
    }

    /**
     * @test
     */
    public function check_should_return_true_if_the_validator_considers_the_value_value_valid()
    {
        $paramValidator = new Parameter('someParam', 1, true, v::intVal());

        $this->assertTrue($paramValidator->check());
    }

    /**
     * @test
     */
    public function check_should_throw_a_Respect_ValidationException_if_the_validator_considers_the_value_value_invalid()
    {
        $this->setExpectedException('\Respect\Validation\Exceptions\ValidationException');

        $paramValidator = new Parameter('someParam', 40.55, true, v::intVal());
        $paramValidator->check();
    }

    /**
     * @test
     */
    public function assert_should_return_true_if_the_validator_considers_the_value_value_valid()
    {
        $paramValidator = new Parameter('someParam', 1, true, v::intVal());

        $this->assertTrue($paramValidator->assert());
    }

    /**
     * @test
     */
    public function assert_should_throw_a_Respect_ValidationException_if_the_validator_considers_the_value_value_invalid()
    {
        $this->setExpectedException('\Respect\Validation\Exceptions\ValidationException');

        $paramValidator = new Parameter('someParam', 40.55, true, v::intVal());
        $paramValidator->assert();
    }

    /**
     * @test
     */
    public function parameterArrayToKvArray_should_take_an_array_of_parameters_and_return_a_kv_array_of_parameter_name_and_value()
    {
        $params = [
            new Parameter('a', 1, false),
            new Parameter('b', ['a' => 'b'], false),
            new Parameter('c', 40.5, false),
            new Parameter('d', 'abc', false),
        ];

        $expected = [
            'a' => 1,
            'b' => ['a' => 'b'],
            'c' => 40.5,
            'd' => 'abc'
        ];

        $this->assertEquals(Parameter::parameterArrayToKvArray($params), $expected);
    }

    protected function getValidationParams($params = [])
    {
        $defaults = [
            'name' => 'someParam',
            'value' => 123,
            'required' => true,
            'validator' => v::intVal(),
            'customErrors' => ['intval' => '{{name}} must be legit']
        ];

        return array_merge($defaults, $params);
    }

    protected function buildParamValidator($params = [])
    {
        $params = $this->getValidationParams($params);

        extract($params);

        return new Parameter($name, $value, $required, $validator, $customErrors);
    }
}
