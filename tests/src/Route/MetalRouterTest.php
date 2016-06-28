<?php

namespace ApiMetal\Tests\Routes;

use ApiMetal\Exception\MetalRouterException;
use ApiMetal\Request\Request;
use ApiMetal\Route\MetalRouter;
use ApiMetal\Route\Route;
use ApiMetal\Route\RouteHandler;
use ApiMetal\Tests\TestCase;
use FastRoute;
use FastRoute\Dispatcher\GroupCountBased;

class MetalRouterTest extends TestCase
{
    /**
     * @test
     */
    public function throw404_should_throw_a_404_exception()
    {
        $this->setExpectedException('\\ApiMetal\\Error\\NotFound');

        MetalRouter::throw404();
    }

    /**
     * @test
     */
    public function throw405_should_throw_a_405_exception()
    {
        $this->setExpectedException('\\ApiMetal\\Error\\MethodNotAllowed');

        MetalRouter::throw405();
    }

    /**
     * @test
     */
    public function throw405_should_throw_a_405_exception_having_message_describing_allowed_methods()
    {
        try {
            MetalRouter::throw405(['GET', 'POST']);
            $this->assertTrue(false);
        } catch (\Exception $e) {
            $expected = 'Method not allowed; not one of: [GET, POST]';
            $this->assertEquals($e->getMessage(), $expected);
        }
    }

    /**
     * @test
     */
    public function throw405_should_throw_a_TypeError_if_argument_is_not_array()
    {
        $this->assertTypeError(function () {
            return MetalRouter::throw405('');
        });
    }

    /**
     * @test
     */
    public function getDispatcher_should_accept_an_array_and_return_a_GroupCountBased()
    {
        // string argument should throw exception
        $this->assertTypeError(function () {
            return MetalRouter::getDispatcher('');
        });

        // array argument should return GroupCountBased
        $this->assertInstanceOf('FastRoute\\Dispatcher\\GroupCountBased', MetalRouter::getDispatcher([]));
    }

    /**
     * @test
     */
    public function getRouteHandlerShould_throw_a_TypeError_if_argument_is_not_an_array()
    {
        $this->assertTypeError(function () {
            return MetalRouter::getRouteHandler('');
        });
    }

    /**
     * @test
     */
    public function getRouteHandlerShould_throw_404_if_array_argument_index_0_is_0()
    {
        $this->setExpectedException('\\ApiMetal\\Error\\NotFound');

        MetalRouter::getRouteHandler([0]);
    }

    /**
     * @test
     */
    public function getRouteHandler_should_throw_405_if_array_argument_index_0_is_1()
    {
        $this->setExpectedException('\\ApiMetal\\Error\\MethodNotAllowed');

        MetalRouter::getRouteHandler([2, [], []]);
    }

    /**
     * @test
     */
    public function getRouteHandler_should_return_a_RouteHandler_when_there_is_a_match()
    {
        $routeHandler = MetalRouter::getRouteHandler([1, '\ApiMetal\Controller\MetalController#getRestaurantDetails', []]);

        $this->assertInstanceOf('\\ApiMetal\\Route\\RouteHandler', $routeHandler);
    }

    /**
     * @test
     */
    public function getController_should_throw_a_TypeError_if_argument_1_is_not_a_Request()
    {
        $this->assertTypeError(function () {
            MetalRouter::getController([], []);
        });
    }

    /**
     * @test
     */
    public function getController_should_throw_a_TypeError_if_argument_2_is_not_a_RouteHandler()
    {
        $this->assertTypeError(function () {
            MetalRouter::getController(new Request, []);
        });
    }

    /**
     * @test
     */
    public function getController_should_return_a_controller_of_the_controller_specified_in_the_RouteHandler()
    {
        $routeHandler = new RouteHandler('\\ApiMetal\\Controller\\MetalController#getRestaurants', []);

        $this->assertInstanceOf(
            'ApiMetal\\Controller\\MetalController',
            MetalRouter::getController(new Request, $routeHandler)
        );
    }

    /**
     * @test
     */
    public function preCheck404_should_do_nothing_if_basePath_is_found_in_server_REQUEST_URI()
    {
        $basePath = '/consumerAPI';
        $server = ['REQUEST_URI' => '/consumerAPI/public/getRestaurants'];

        MetalRouter::preCheck404(new Request($server), $basePath);
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function preCheck404_should_throw_404_if_basePath_is_not_found_in_server_REQUEST_URI()
    {
        $this->setExpectedException('\\ApiMetal\\Error\\NotFound');

        $basePath = '/consumerz';
        $server = ['REQUEST_URI' => '/consumerAPI/public/getRestaurants'];

        MetalRouter::preCheck404(new Request($server), $basePath);
    }

    /**
     * @test
     */
    public function getResponder_should_throw_a_typeError_if_argument_1_request_is_not_a_Request()
    {
        $this->assertTypeError(function () {
            return MetalRouter::getResponder([]);
        });
    }

    /**
     * @test
     */
    public function getResponder_should_throw_a_typeError_if_argument_2_routes_is_not_an_array()
    {
        $this->assertTypeError(function () {
            return MetalRouter::getResponder(new Request, '');
        });
    }

    /**
     * @test
     */
    public function getResponder_should_throw_a_MetalRouterException_if_the_matching_routes_controller_class_cannot_be_found()
    {
        $request = new Request(['REQUEST_URI' => '/api/route/bad-controller', 'REQUEST_METHOD' => 'GET']);

        try {
            MetalRouter::getResponder($request, $this->getRoutes());
            $this->assertFalse(true);
        } catch (MetalRouterException $e) {
            $expectedMessage = 'ApiMetal | MetalException | MetalRouterException - Error getting matched controller for request: /api/route/bad-controller';
            $this->assertEquals($e->getMessage(), $expectedMessage);
        }
    }

    /**
     * @test
     */
    public function getResponder_should_throw_a_MetalRouterException_if_the_matching_routes_controller_method_cannot_be_found()
    {
        $request = new Request(['REQUEST_URI' => '/api/route/good-controller-missing-method', 'REQUEST_METHOD' => 'GET']);

        try {
            MetalRouter::getResponder($request, $this->getRoutes());
            $this->assertFalse(true);
        } catch (MetalRouterException $e) {
            $expectedMessage = 'ApiMetal | MetalException | MetalRouterException - Error getting matched controller method for controller: \ControllerClass and request: /api/route/good-controller-missing-method';
            $this->assertEquals($e->getMessage(), $expectedMessage);
        }
    }

    /**
     * @test
     */
    public function getResponder_should_throw_a_MetalRouterException_if_the_matching_routes_controller_cannot_be_instantiated()
    {
        $request = new Request(['REQUEST_URI' => '/api/route/missing-controller', 'REQUEST_METHOD' => 'GET']);

        try {
            MetalRouter::getResponder($request, $this->getRoutes());
            $this->assertFalse(true);
        } catch (MetalRouterException $e) {
            $expectedMessage = 'ApiMetal | MetalException | MetalRouterException - Error instantiating matched controller: ; Class \'\MissingController\' not found';
            $this->assertEquals($e->getMessage(), $expectedMessage);
        }
    }

    /**
     * @test
     */
    public function getResponder_should_return_a_callable_if_the_matching_routes_controller_is_found_and_instantiated_and_the_method_is_found()
    {
        $request = new Request(['REQUEST_URI' => '/api/route/good-controller-good-method', 'REQUEST_METHOD' => 'GET']);

        $responder = MetalRouter::getResponder($request, $this->getRoutes());
        $this->assertTrue(is_callable($responder));
    }

    public function getRoutes()
    {
        return [
            new Route('GET', '/api/route/bad-controller', ''),
            new Route('GET', '/api/route/missing-controller', '\\MissingController#method'),
            new Route('GET', '/api/route/good-controller-missing-method', '\\ControllerClass'),
            new Route('GET', '/api/route/good-controller-bad-method', '\\ApiMetal\\Tests\\MockController#badMethod'),
            new Route('GET', '/api/route/good-controller-good-method', '\\ApiMetal\\Tests\\MockController#goodMethod')
        ];
    }
}
