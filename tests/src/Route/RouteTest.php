<?php

namespace ApiMetal\Tests\Routes;

use ApiMetal\Route\Route;

class RouteTest extends \ApiMetal\Tests\TestCase
{
    /**
     * @test
     */
    public function constructor_should_take_3_arguments_and_return_a_Route_with_httpMethod_path_and_handler()
    {
        $route = new Route('GET', '/some/path', 'someHandler');

        $this->assertInstanceOf('\ApiMetal\Route\Route', $route);
        $this->assertEquals($route->getHttpMethod(), 'GET');
        $this->assertEquals($route->getPath(), '/some/path');
        $this->assertEquals($route->getHandler(), 'someHandler');
    }

    /**
     * @test
     */
    public function constructor_should_throw_TypeError_if_argument_1_httpMethod_is_not_a_string()
    {
        $this->assertTypeError(function () {
            return new Route([], '', '');
        });
    }

    /**
     * @test
     */
    public function constructor_should_throw_TypeError_if_argument_2_path_is_not_a_string()
    {
        $this->assertTypeError(function () {
            return new Route('', [], '');
        });
    }

    /**
     * @test
     */
    public function constructor_should_throw_TypeError_if_argument_3_handler_is_not_a_string()
    {
        $this->assertTypeError(function () {
            return new Route('', '', []);
        });
    }

    /**
     * @test
     */
    public function setHttpMethod_sets_httpMethod_and_returns_the_object()
    {
        $route = new Route('', '', '');
        $expected = 'POST';

        $route = $route->setHttpMethod($expected);

        $this->assertInstanceOf('\ApiMetal\Route\Route', $route);
        $this->assertEquals($route->getHttpMethod(), $expected);
    }

    /**
     * @test
     */
    public function setPath_sets_path_and_returns_the_object()
    {
        $route = new Route('', '', '');
        $expected = '/some/path';

        $route = $route->setPath($expected);

        $this->assertInstanceOf('\ApiMetal\Route\Route', $route);
        $this->assertEquals($route->getPath(), $expected);
    }

    /**
     * @test
     */
    public function setHandler_sets_handler_and_returns_the_object()
    {
        $route = new Route('', '', '');
        $expected = 'someHandler';

        $route = $route->setHandler($expected);

        $this->assertInstanceOf('\ApiMetal\Route\Route', $route);
        $this->assertEquals($route->getHandler(), $expected);
    }

    /**
     * @test
     */
    public function getHttpMethod_gets_the_value_of_this_httpMethod()
    {
        $expected = 'GET';
        $route = new Route($expected, '', '');

        $this->assertEquals($route->getHttpMethod(), $expected);
    }

    /**
     * @test
     */
    public function getPath_gets_the_value_of_this_path()
    {
        $expected = '/some/path';
        $route = new Route('', $expected, '');

        $this->assertEquals($route->getPath(), $expected);
    }

    /**
     * @test
     */
    public function getHandler_gets_the_value_of_this_handler()
    {
        $expected = 'someHandler';
        $route = new Route('', '', $expected);

        $this->assertEquals($route->getHandler(), $expected);
    }
}
