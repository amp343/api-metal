<?php

namespace ApiMetal\Controller;

use ApiMetal\Controller\Validation\ValidationTrait; use ApiMetal\Exception\MetalControllerException;
use ApiMetal\Request\Request;
use ApiMetal\Response\Response;
use ApiMetal\Route\RouteHandler;

class MetalController
{
    /**
     * Import a module of validation-specific concerns
     * that are valid within the context of this class.
     */
    use ValidationTrait;

    /**
     * A Request representing a server request
     *
     * @var ApiMetal\Request\Request
     */
    protected $request;

    /**
     * A Response representing the outgoing Response
     *
     * @var ApiMetal\Response\Response
     */
    protected $response;

    /**
     * A RouteHandler instance describing how the matched
     * route should be handled
     *
     * @var ApiMetal\Route\RouteHandler
     */
    protected $routeHandler;

    /**
     * Whether params have been validated by this controller
     *
     * @var bool
     */
    protected $paramsValidated = false;

    /**
     * The Error class to be thrown in the case
     * of invalid parameters
     */
    protected static $paramValidationErrorClass = '\\ApiMetal\\Error\\UnprocessableEntity'; // 422

    /**
     * @param ApiMetal\Request\Request $request  A Request instance describing
     *                                              the server request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->response = new Response;

        // let extended classes hook in to pass a group of initializers
        // without necessarily redefining the constructor
        $this->initialize();
    }

    /**
     * @codeCoverageIgnore
     *
     * Execute some code at the end of default construction;
     * to be used by extended classes to avoid redefining constructor
     */
    protected function initialize()
    {
    }

    /**
     * Set the value of $this->response->body
     * to the result of $this->$methodName(),
     * and return $this; ie, populate the response
     * body in preparation for responding
     *
     * @param  string $methodName [description]
     * @throws ApiMetal\Exception\MetalControllerException   If the method was
     *                                                          called before
     *                                                          ::validate()
     * @return ApiMetal\Controller\MetalController           This
     */
    public function fulfill(string $methodName): MetalController
    {
        try {
            $this->getResponse()->setBody($this->$methodName());

            if (!$this->getParamsValidated()) {
                throw new MetalControllerException('Controller method ' . $methodName . ' may not return without calling ::validate()');
            }
        } catch (\Exception $e) {
            $this->getResponse()->setError($e);
        }

        return $this;
    }

    /**
     * @codeCoverageIgnore
     *
     * Convenience method for calling ->respond() on $this->response
     */
    public function respond()
    {
        $this->getResponse()->respond();
    }

    /**
     * Convenience method for calling ->getHeader() on $this->request
     *
     * @param  string $key The key whose value to return from $this->request->headers
     * @return mixed       The value corresponding to index $key
     */
    public function getHeader(string $key)
    {
        return ($this->getRequest())->getHeader($key);
    }

    /**
     * Convenience method for calling ->getParam() on $this->request
     *
     * @param  string $paramName    The key whose value to return from
     *                              $this->request->params
     * @return mixed                The value corresponding to index $key
     */
    public function getParam(string $paramName = null)
    {
        return $this->getRequest()->getParam($paramName);
    }

    /**
     * Return the value of $this->request
     *
     * @return ApiMetal\Request\Request The value of $this->request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * Return the value of $this->response
     *
     * @return ApiMetal\Response\Response The value of $this->response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * Return the value of $this->paramsValidated
     *
     * @return bool The value of $this->paramsValidated
     */
    public function getParamsValidated(): bool
    {
        return $this->paramsValidated;
    }

    public function setParamsValidated(bool $paramsValidated) : MetalController
    {
        $this->paramsValidated = $paramsValidated;

        return $this;
    }
}
