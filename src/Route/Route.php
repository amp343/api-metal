<?php

namespace ApiMetal\Route;

class Route
{
    /**
     * The httpMethod by which the route can be accessed
     *
     * @var string
     */
    protected $httpMethod;

    /**
     * The path by which the route can be accessed
     *
     * @var string
     */
    protected $path;

    /**
     * The intended handler, ie, a #-delimited string, where
     * the pre-# portion describes the intended controller class,
     * and the post-# portion describes the intended controller method;
     * ie: PublicController#getRestaurants
     *
     * @var string
     */
    protected $handler;

    /**
     * @param string $httpMethod The httpMethod by which the route can be accessed
     * @param string $path       The path by which the route can be accessed
     * @param string $handler    The intended controller-method handler string,
     *                           ie, 'Namespace\PublicController#method'
     */
    public function __construct(string $httpMethod, string $path, string $handler)
    {
        $this->httpMethod = $httpMethod;
        $this->path = $path;
        $this->handler = $handler;
    }

    /**
     * Return the value of $this->httpMethod
     *
     * @return string   The value of $this->httpMethod
     */
    public function getHttpMethod(): string
    {
        return $this->httpMethod;
    }

    /**
     * Return the value of $this->path
     *
     * @return string   The value of $this->path
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Return the value of $this->handler
     *
     * @return string   The value of $this->handler
     */
    public function getHandler(): string
    {
        return $this->handler;
    }

    /**
     * Set the value of $this->httpMethod and return $this
     *
     * @param string                    $httpMethod The intended value of
     *                                              $this->httpMethod
     * @return ApiMetal\Route\Route                 This
     */
    public function setHttpMethod(string $httpMethod)
    {
        $this->httpMethod = $httpMethod;

        return $this;
    }

    /**
     * Set the value of $this->path and return $this
     *
     * @param string                    $path   The intended value of $this->path
     * @return ApiMetal\Route\Route          This
     */
    public function setPath(string $path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Set the value of $this->handler and return $this
     *
     * @param string                    $handler    The intended value of
     *                                              $this->handler
     * @return ApiMetal\Route\Route              This
     */
    public function setHandler(string $handler)
    {
        $this->handler = $handler;

        return $this;
    }
}
