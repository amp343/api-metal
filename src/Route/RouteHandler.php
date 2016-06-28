<?php

namespace ApiMetal\Route;

class RouteHandler
{
    /**
     * A string describing the intended #-delimited controller-method
     * handler, ie, 'PublicController#getRestaurants'
     *
     * @var string
     */
    protected $handler = null;

    /**
     * An k/v array of matched route parameters
     *
     * @var array
     */
    protected $routeParams = [];

    /**
     * The name of the matched controller class,
     * ie, derived from $handler
     *
     * @var string
     */
    protected $controllerClass = null;

    /**
     * The name of the matched controller method,
     * ie, derived from $handler
     *
     * @var string
     */
    protected $controllerMethod = null;

    /**
     * @param string $handler     The #-delimited controller-method handler string
     * @param array  $routeParams An array describing matched route params
     */
    public function __construct(string $handler, array $routeParams = [])
    {
        $this->handler = $handler;
        $this->routeParams = $routeParams;

        $controllerComponents = explode('#', $this->handler);
        $this->controllerClass = $controllerComponents[0] ?? null;
        $this->controllerMethod = $controllerComponents[1] ?? null;
    }

    /**
     * Get the value of $this->routeParams
     *
     * @return array The value of $this->routeParams
     */
    public function getRouteParams(): array
    {
        return $this->routeParams;
    }

    /**
     * Get the value of $this->handler
     *
     * @return string The value of $this->handler
     */
    public function getHandler(): string
    {
        return $this->handler;
    }

    /**
     * Get the value of $this->controllerClass, ie, the name
     * of the matched controller class as derived from $handler
     *
     * @return string|null The value of $this->controllerClass
     */
    public function getControllerClass()
    {
        return $this->controllerClass;
    }

    /**
     * Get the value of $this->controllerMethod, ie, the name
     * of the matched controller method as derived from $handler
     *
     * @return string|null The value of $this->controllerMethod
     */
    public function getControllerMethod()
    {
        return $this->controllerMethod;
    }
}
