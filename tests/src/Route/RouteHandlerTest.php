<?php

namespace ApiMetal\Tests\Routes;

use ApiMetal\Route\RouteHandler;

class RouteHandlerTest extends \ApiMetal\Tests\TestCase
{
    /**
     * @test
     */
    public function constructor_should_take_3_arguments_and_return_a_RouteHandler_with_handler_and_routeParams()
    {
        $routeParams = ['a' => 'b', 'c' => 'd'];
        $handlerString = $this->getHandlerString();

        $routeHandler = new RouteHandler($handlerString, $routeParams);

        $this->assertInstanceOf('\ApiMetal\Route\RouteHandler', $routeHandler);
        $this->assertEquals($routeHandler->getHandler(), $handlerString);
        $this->assertEquals($routeHandler->getRouteParams(), $routeParams);
    }

    /**
     * @test
     */
    public function constructor_should_throw_a_TypeError_if_argument_1_handler_is_not_a_string()
    {
        $this->assertTypeError(function () {
            return new RouteHandler([], '');
        });
    }

    /**
     * @test
     */
    public function constructor_should_throw_a_TypeError_if_argument_2_routeParams_is_not_an_array()
    {
        $this->assertTypeError(function () {
            return new RouteHandler('', '');
        });
    }

    /**
     * @test
     */
    public function getControllerClass_should_return_the_substring_of_this_handler_up_to_the_pound_sign_delimiter()
    {
        $routeHandler = new RouteHandler($this->getHandlerString(), []);
        $expected = explode('#', $this->getHandlerString())[0];

        $this->assertEquals($routeHandler->getControllerClass(), $expected);
    }

    /**
     * @test
     */
    public function getControllerMethod_should_return_the_substring_of_this_handler_after_the_pound_sign_delimiter()
    {
        $routeHandler = new RouteHandler($this->getHandlerString(), []);
        $expected = explode('#', $this->getHandlerString())[1];

        $this->assertEquals($routeHandler->getControllerMethod(), $expected);
    }

    protected function getHandlerString()
    {
        return '\ApiMetal\Controller\PublicController#getRestaurants';
    }
}
