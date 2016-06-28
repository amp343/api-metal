<?php

namespace ApiMetal\Tests\Controller\Validation;

use ApiMetal\Controller\MetalController;
use ApiMetal\Controller\Parameter\Parameter;
use ApiMetal\Error\UnprocessableEntity;
use ApiMetal\Request\Request;
use ApiMetal\Route\RouteHandler;
use ApiMetal\Tests\TestCase;
use Respect\Validation\Validator;

class ValidationTraitTest extends TestCase
{
    /**
     * @test
     */
    public function validateAllowedParams_should_throw_a_TypeError_if_argument_1_allowedParams_is_not_an_array()
    {
        $this->assertTypeError(function () {
            return (new MetalController(new Request, new RouteHandler('')))->validateAllowedParams('', []);
        });
    }

    /**
     * @test
     */
    public function validateAllowedParams_should_throw_a_TypeError_if_argument_2_sentParams_is_not_an_array()
    {
        $this->assertTypeError(function () {
            return (new MetalController(new Request, new RouteHandler('')))->validateAllowedParams([], '');
        });
    }

    /**
     * @test
     */
    public function validateAllowedParams_should_throw_an_UnprocessableEntity_when_some_sentParams_are_not_in_allowedParams()
    {
        $sentParams = ['a', 'b', 'c', 'd'];
        $allowedParams = ['a', 'b', 'c'];
        $controller = new MetalController(new Request, new RouteHandler(''));

        $expectedMessage = 'The d parameter is not permitted';

        try {
            $controller->validateAllowedParams($allowedParams, $sentParams);
        } catch (UnprocessableEntity $e) {
            $this->assertEquals($e->getMessage(), $expectedMessage);
        }
    }

    /**
     * @test
     */
    public function validateAllowedParams_should_return_true_if_all_params_pass_validation()
    {
        $sentParams = ['a', 'b', 'c', 'd'];
        $allowedParams = ['a', 'b', 'c', 'd'];
        $controller = new MetalController(new Request, new RouteHandler(''));

        $this->assertTrue($controller->validateAllowedParams($allowedParams, $sentParams));
    }

    /**
     * @test
     */
    public function validateRequiredParams_should_throw_a_TypeError_if_argument_1_params_is_not_an_array()
    {
        $this->assertTypeError(function () {
            return (new MetalController(new Request, new RouteHandler('')))->validateRequiredParams('');
        });
    }

    /**
     * @test
     */
    public function validateRequiredParams_should_return_true_if_all_params_pass_validation()
    {
        $params = [
            new Parameter('paramA', 100, true),
            new Parameter('paramB', null, false)
        ];

        $this->assertTrue(
            (new MetalController(new Request, new RouteHandler('')))->validateRequiredParams($params)
        );
    }

    /**
     * @test
     */
    public function validateRequiredParams_should_return_throw_an_UnprocessableEntity_by_default_if_some_validation_fails()
    {
        $params = [
            new Parameter('paramA', 100, true),
            new Parameter('paramB', null, true)
        ];

        $expectedMessage = 'The paramB parameter is required.';

        try {
            (new MetalController(new Request, new RouteHandler('')))->validateRequiredParams($params);
        } catch (UnprocessableEntity $e) {
            $this->assertEquals($e->getMessage(), $expectedMessage);
        }
    }

    /**
     * @test
     */
    public function mapParameters_should_throw_a_TypeError_if_argument_1_params_is_not_an_array()
    {
        $this->assertTypeError(function () {
            return (new MetalController(new Request, new RouteHandler('')))->mapParameters('');
        });
    }

    /**
     * @test
     */
    public function mapParamters_should_take_a_dsl_array_of_parameter_validation_requirements_and_return_an_array_of_Parameter_s_representing_those_requirements()
    {
        $controller = new MetalController(new Request, new RouteHandler(''));
        ($controller->getRequest())->setParams(['biz_id' => 1, 'consumer_acct_id' => 100]);

        $params = [
            'biz_id' => ['required' => true, 'type' => 'positiveNonZeroInt'],
            'consumer_acct_id' => ['required' => true, 'type' => 'nonZeroInt'],
            'latitude' => ['required' => false, 'type' => 'latitude', 'default' => 40.5],
            'longitude' => ['required' => false, 'type' => 'longitude', 'default' => null],
            'notes' => ['required' => false]
        ];

        $mappedParams = $controller->mapParameters($params);

        // biz_id
        $this->assertEquals($mappedParams[0]->getName(), 'biz_id');
        $this->assertTrue($mappedParams[0]->getRequired());
        $this->assertEquals($mappedParams[0]->getValue(), 1);
        $this->assertInstanceOf('\\Respect\\Validation\\Validator', $mappedParams[0]->getValidator());
        $this->assertEmpty($mappedParams[0]->getCustomErrors());

        // consumer_acct_id
        $this->assertEquals($mappedParams[1]->getName(), 'consumer_acct_id');
        $this->assertTrue($mappedParams[1]->getRequired());
        $this->assertEquals($mappedParams[1]->getValue(), 100);
        $this->assertInstanceOf('\\Respect\\Validation\\Validator', $mappedParams[1]->getValidator());
        $this->assertNotEmpty($mappedParams[1]->getCustomErrors());

        // latitude
        $this->assertEquals($mappedParams[2]->getName(), 'latitude');
        $this->assertFalse($mappedParams[2]->getRequired());
        $this->assertEquals($mappedParams[2]->getValue(), 40.5);
        $this->assertInstanceOf('\\Respect\\Validation\\Validator', $mappedParams[2]->getValidator());
        $this->assertEmpty($mappedParams[2]->getCustomErrors());

        // longitude
        $this->assertEquals($mappedParams[3]->getName(), 'longitude');
        $this->assertFalse($mappedParams[3]->getRequired());
        $this->assertNull($mappedParams[3]->getValue());
        $this->assertInstanceOf('\\Respect\\Validation\\Validator', $mappedParams[3]->getValidator());
        $this->assertEmpty($mappedParams[3]->getCustomErrors());

        // longitude
        $this->assertEquals($mappedParams[4]->getName(), 'notes');
        $this->assertFalse($mappedParams[4]->getRequired());
        $this->assertNull($mappedParams[4]->getValue());
        $this->assertNull($mappedParams[4]->getValidator());
        $this->assertEmpty($mappedParams[4]->getCustomErrors());
    }

    /**
     * @test
     */
    public function validateParamValue_should_throw_a_TypeError_if_parameter_1_parameter_is_not_a_Parameter()
    {
        $this->assertTypeError(function () {
            $controller = new MetalController(new Request, new RouteHandler(''));
            return $controller->validateParamValue('');
        });
    }

    /**
     * @test
     */
    public function validateParamValue_should_return_true_if_the_Parameter_validates()
    {
        $controller = new MetalController(new Request, new RouteHandler(''));
        $parameter = new Parameter('someParam', 10, true, Validator::intVal());

        $this->assertTrue($controller->validateParamValue($parameter));
    }

    /**
     * @test
     */
    public function validateParamValue_should_throw_an_UnprocessableEntity_if_the_parameter_does_not_validate()
    {
        $controller = new MetalController(new Request, new RouteHandler(''));
        $parameter = new Parameter('someParam', [], true, Validator::stringType());

        try {
            $controller->validateParamValue($parameter);
            $this->assertTrue(false);
        } catch (UnprocessableEntity $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * @test
     */
    public function validateParamValues_should_return_true_if_validation_passed_for_all_params()
    {
        $controller = new MetalController(new Request, new RouteHandler(''));
        $parameters = [
            new Parameter('someParam', 'abc', true, Validator::stringType()),
            new Parameter('someParam', 1, true, Validator::intVal()),
            new Parameter('someParam', null, false, Validator::stringType())
        ];

        $this->assertTrue($controller->validateParamValues($parameters));
    }

    /**
     * @test
     */
    public function validateParamValues_should_throw_an_UnprocessableEntity_if_validation_does_not_pass_for_all_parameters()
    {
        $controller = new MetalController(new Request, new RouteHandler(''));
        $parameters = [
            new Parameter('someParam', 'abc', true, Validator::stringType()),
            new Parameter('someParam', 1, true, Validator::stringType()),
            new Parameter('someParam', null, false, Validator::stringType())
        ];

        try {
            $controller->validateParamValues($parameters);
            $this->assertTrue(false);
        } catch (UnprocessableEntity $e) {
            $this->assertTrue(true);
        }
    }

    // TODO: write actual tests for ::validate()
}
