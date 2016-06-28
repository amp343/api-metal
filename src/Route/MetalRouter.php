<?php

namespace ApiMetal\Route;

use ApiMetal\Controller\MetalController;
use ApiMetal\Error;
use ApiMetal\Exception\MetalRouterException;
use ApiMetal\Request\Request;
use FastRoute;
use FastRoute\Dispatcher\RegexBasedAbstract;

/**
 * MetalRouter: Simple functional router implementing FastRoute.
 * The intent is to be able to just call ::handleRequest to fully handle a request,
 * or at least ::getResponder() to obtain the matching controller/method
 */
class MetalRouter
{
    /**
     * Throw a ApiMetal\Error\NotFound
     *
     * @throws ApiMetal\Error\NotFound   The exception describing a 404 case
     */
    public static function throw404()
    {
        $message = 'The requested API method does not exist';
        throw new Error\NotFound($message);
    }

    /**
     * Throw a ApiMetal\Error\MethodNotAllowed
     *
     * @throws ApiMetal\Error\MethodNotAllowed   The exception describing
     *         										a 405 case
     */
    public static function throw405(array $allowedMethods = [])
    {
        $message = 'Method not allowed; not one of: [' . implode(', ', $allowedMethods) . ']';
        throw new Error\MethodNotAllowed($message);
    }

    /**
     * Given an array of Routes, return a FastRoute dispatcher instance
     *
     * @param  array  $routes   An array of Routes
     * @return FastRoute\Dispatcher\RegexBasedAbstract  A dispatcher built
     *                                                  from $routes
     */
    public static function getDispatcher(array $routes): RegexBasedAbstract
    {
        return \FastRoute\simpleDispatcher(function (\FastRoute\RouteCollector $r) use ($routes) {
            foreach ($routes as $route) {
                $r->addRoute($route->getHttpMethod(), $route->getPath(), $route->getHandler());
            }
        });
    }

    /**
     * Given a Request and a FastRoute Dispatcher, return a RouteHandler
     * for the matched route, or throw an error describing a non-match
     *
     * @param  ApiMetal\Request\Request   $request  The http request representation
     * @param  FastRoute\Dispatcher\RegexBasedAbstract  $dispatcher A FastRoute Dispatcher
     * @return ApiMetal\Route\RouteHandler  A RouteHandler describing how
     *                                         the given Request should be handled
     */
    protected static function resolveRoute(Request $request, RegexBasedAbstract $dispatcher): RouteHandler
    {
        return self::getRouteHandler(
            $dispatcher->dispatch(
                $request->getHttpMethod(),
                $request->getUriPath()
            )
        );
    }

    /**
     * Given a FastRoute route into array; that is,
     * a [0, 1, 2] indexed array, where:
     * - 0 describes the outcome of dispatching
     * - 1 describes the intended handler
     * - 2 describes matched route parameters and values
     * ... return a RouteHandler instance that decorates and more
     * semantically describes $routeInfo
     *
     * @param  array                            $routeInfo  The [0, 1, 2] FastRoute
     *                                                      route info array
     * @return ApiMetal\Route\RouteHandler               The $routeInfo array
     *                                                      as cast to a formal
     *                                                      RouteHandler instance
     */
    public static function getRouteHandler(array $routeInfo): RouteHandler
    {
        switch ($routeInfo[0]) {
            case FastRoute\Dispatcher::NOT_FOUND:
                self::throw404();
                // ... 404 Not found
            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                self::throw405($routeInfo[1]);
                // ... 405 Method Not Allowed
            case FastRoute\Dispatcher::FOUND:
                return new RouteHandler($routeInfo[1], $routeInfo[2]);
                // ... call $handler with $vars
        }
    }

    /**
     * Given a Request and a RouteHandler, return an instance
     * of the matched controller, with RouteHandler route params
     * folded into the request
     *
     * @param  ApiMetal\Request\Request    $request      The Request
     * @param  ApiMetal\Route\RouteHandler $routeHandler The RouteHandler matched
     *                                                      to the request
     * @return ApiMetal\Controller\MetalController       An instance of the
     *                                                      matched controller as
     *                                                      described by $routeHandler
     */
    public static function getController(Request $request, RouteHandler $routeHandler): MetalController
    {
        $controllerClass = $routeHandler->getControllerClass();

        // merge matched route params into $request
        //
        $request->setParams(
            $routeHandler->getRouteParams(),
            $request->getParams()
        );

        return new $controllerClass($request);
    }

    /**
     * Given a Request and a base path, quickly determine
     * whether the basePath is present in the Request->path
     * and throw a 404 if not. Used as a pre-check to avoid
     * heavy lifting in the case of obvious non-matches
     *
     * @param  Request $request  The Request
     * @param  string  $basePath The basePath to ensure exists in the Request
     */
    public static function preCheck404(Request $request, string $basePath)
    {
        if (strpos($request->getServer()['REQUEST_URI'] ?? '', $basePath) === false) {
            self::throw404();
        }
    }

    /**
     * Given a Request and an array of Routes, return a callable
     * that, when executed, will fulfill and send the intended response
     *
     * @param  ApiMetal\Request\Request  $request The Request
     * @param  array                        $routes  An array of Routes
     * @return callable                              A callable that, when
     *                                               executed, will fulfill and
     *                                               send the intended response
     */
    public static function getResponder(Request $request, array $routes): callable
    {
        $dispatcher = self::getDispatcher($routes);
        $routeHandler = self::resolveRoute($request, $dispatcher);

        // make sure the requested controller name can be found
        //
        if (!$routeHandler->getControllerClass()) {
            throw new MetalRouterException('Error getting matched controller for request: ' . $request->getUriPath());
        }

        // make sure the requested controller method can be found
        //
        if (!$routeHandler->getControllerMethod()) {
            throw new MetalRouterException('Error getting matched controller method for controller: ' . $routeHandler->getControllerClass() . ' and request: ' . $request->getUriPath());
        }

        // make sure the requested controller can be instantiated
        //
        try {
            // build a controller instance
            $controller = self::getController($request, $routeHandler);
        } catch (\Error $e) {
            throw new MetalRouterException('Error instantiating matched controller: ' . !$routeHandler->getControllerClass() . '; ' . $e->getMessage());
        }

        // let the controller method throw its own exceptions
        //
        $controllerMethod = $routeHandler->getControllerMethod();

        // return a callable which, when called, will send a response
        //
        return function () use ($controller, $controllerMethod) {
            // @codeCoverageIgnoreStart
            $controller->fulfill($controllerMethod)->respond();
            // @codeCoverageIgnoreEnd
        };
    }

    /**
     * @codeCoverageIgnore
     *
     * Given a Request and array of routes, build a callable that
     * describes the intended execution path for the Request, then
     * execute it, thus fulfilling and sending the response
     *
     * @param ApiMetal\Request\Request $request  The Request
     * @param array                       $routes   An array of Routes
     * @return mixed The result of the executed callable; in practice, no
     *               response, as the callable will fulfill and send a Respones.
     */
    public static function handleRequest(Request $request, array $routes)
    {
        $responder = self::getResponder($request, $routes);

        return $responder();
    }
}
