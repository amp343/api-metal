<?php

namespace ApiMetal\Tests\Controller;

use ApiMetal\Controller\MetalController;
use ApiMetal\Exception\MetalControllerException;
use ApiMetal\Request\Request;
use ApiMetal\Tests\MockController;
use ApiMetal\Tests\TestCase;

class MetalControllerTest extends TestCase
{
    /**
     * @test
     */
    public function construct_should_throw_a_TypeError_if_argument_1_request_is_not_a_Request()
    {
        $this->assertTypeError(function () {
            return new MetalController([]);
        });
    }

    /**
     * @test
     */
    public function getParam_should_get_the_value_of_the_matching_param_or_null()
    {
        $request = ['a' => 1, 'b' => 2];

        $controller = new MetalController(new Request([], $request));

        $this->assertEquals($controller->getParam('a'), 1);
        $this->assertEquals($controller->getParam('b'), 2);
        $this->assertEquals($controller->getParam('c'), null);
    }

    /**
     * @test
     */
    public function getParam_should_throw_a_TypeError_if_argument_1_key_is_not_a_string()
    {
        $this->assertTypeError(function () {
            $controller = new MetalController(new Request);
            return $controller->getParam([]);
        });
    }

    /**
     * @test
     */
    public function getHeader_should_throw_a_TypeError_if_argument_1_key_is_not_a_string()
    {
        $this->assertTypeError(function () {
            $controller = new MetalController(new Request);
            return $controller->getHeader([]);
        });
    }

    /**
     * @test
     */
    public function getHeader_should_return_the_value_of_this_headers_with_the_given_key()
    {
        $server = ['HTTP_some-header' => 12345];
        $controller = new MetalController(new Request($server));

        $this->assertEquals($controller->getHeader('some-header'), 12345);
        $this->assertEquals($controller->getHeader('some-other-header'), null);
    }

    /**
     * @test
     */
    public function fulfill_should_throw_a_TypeError_if_argument_1_methodName_is_not_a_string()
    {
        $this->assertTypeError(function () {
            $controller = new MetalController(new Request);
            $controller->fulfill([]);
        });
    }

    /**
     * @test
     */
    public function fulfill_should_throw_a_MetalControllerException_if_validate_has_not_been_called_first()
    {
        $controller = new MockController(new Request);
        $expectedMessage = 'ApiMetal | MetalException | MetalControllerException - Controller method goodMethod may not return without calling ::validate()';

        try {
            $controller->fulfill('goodMethod');
        } catch (MetalControllerException $e) {
            $this->assertEquals($e->getMessage(), $expectedMessage);
        }
    }

    /**
     * @test
     */
    public function fulfill_should_set_the_value_of_this_response_body_to_the_return_value_of_the_given_method()
    {
        $controller = new MockController(new Request);
        $controller->validate([]);

        $expected = 'abc';
        $result = $controller->fulfill('goodMethod');
        $value = $controller->getResponse()->getBody();

        $this->assertEquals($value, $expected);
        $this->assertInstanceOf('\\ApiMetal\\Controller\\MetalController', $result);
    }

    /**
     * @test
     */
    public function fulfill_should_throw_an_Error_if_invoking_methodName_fails()
    {
        $controller = new MockController(new Request);
        $controller->validate([]);
        $expected = 'Call to undefined method ApiMetal\Tests\MockController::missingMethod()';

        try {
            $controller->fulfill('missingMethod');
        } catch (\Error $e) {
            $this->assertEquals($e->getMessage(), $expected);
        }
    }

    /**
     * @test
     */
    public function fulfill_should_set_a_response_error_if_the_given_methodName_throws_one()
    {
        $expectedCode = 403;
        $expectedBody = [
            'errors' => [
                ['status' => 403, 'detail' => 'this is forbidden', 'code' => 0]
            ]
        ];

        $controller = new MockController(new Request);
        $controller->validate([]);

        $result = $controller->fulfill('exceptionMethod');
        $response = $controller->getResponse();

        $this->assertEquals($response->getStatus(), $expectedCode);
        $this->assertEquals($response->getBody(), $expectedBody);
    }

    /**
     * @test
     */
    public function getRequest_should_return_the_value_of_this_request()
    {
        $request = new Request;
        $controller = new MetalController($request);

        $this->assertEquals($controller->getRequest(), $request);
    }

    /**
     * @test
     */
    public function getParamsValidated_should_return_the_value_of_this_paramsValidated()
    {
        $controller = new MetalController(new Request);

        $this->assertEquals($controller->getParamsValidated(), false);
        $controller->setParamsValidated(true);
        $this->assertEquals($controller->getParamsValidated(), true);
    }

    /**
     * @test
     */
    public function setParamsValidated_should_throw_a_TypeError_if_argument_1_paramsValidated_is_not_bool()
    {
        $this->assertTypeError(function () {
            $controller = new MetalController(new Request);
            $controller->setParamsValidated([]);
        });
    }

    /**
     * @test
     */
    public function setParamsValidated_should_set_the_value_of_this_paramsValidated()
    {
        $expected = true;
        $controller = new MetalController(new Request);
        $controller->setParamsValidated($expected);

        $this->assertEquals($controller->getParamsValidated(), $expected);
    }
}
