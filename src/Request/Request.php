<?php

namespace ApiMetal\Request;

use ApiMetal\Auth\BasicAuth\BasicAuth;

class Request
{
    /**
     * An array of server request attributes;
     * ie, $_SERVER
     *
     * @var array
     */
    protected $server = [];

    /**
     * An array of request headers
     * ie, $_SERVER['HTTP_*']
     *
     * @var array
     */
    protected $headers = [];

    /**
     * An array of request parameters
     * ie, $_REQUEST or equivalent
     *
     * @var array
     */
    protected $params = [];

    /**
     * The http request method
     *
     * @var string
     */
    protected $httpMethod = null;

    /**
     * The uri path of the request
     *
     * @var string
     */
    protected $uriPath = null;

    /**
     * Request headers, transformed for
     * consistent retrieval
     *
     * @var array
     */
    protected $ucHeaders = [];

    /**
     * @param array $server The server attributes describing the request
     */
    public function __construct(array $server = [], array $params = [])
    {
        $this->server = $server;

        $this->params = $params;
        $this->headers = $this->initHeaders();
        $this->httpMethod = $this->initHttpMethod();
        $this->uriPath = $this->initUriPath();

        // NOTE:
        // we transform the headers here to comply with spec, so that
        // $this->getHeader('key') can find the correct header in a
        // case-insensitive way. The approach: brute-force transform headers
        // to uppercase keys on each request; then have ->getHeader() consult
        // $this->ucHeaders; Alternatively, we could transform the headers
        // once, on-demand, the first time ->getHeader() is called, then cache
        // that value for subsequent reference. But, given that this operation
        // can be performed one million times on a reasonbly complex array in
        // ~0.3 seconds, there is probably not a significant performance case.
        //
        $this->setUcHeaders();
    }

    // PUBLIC

    /**
     * Get the value of $this->server
     *
     * @return array The value of $this->server
     */
    public function getServer(): array
    {
        return $this->server;
    }

    /**
     * Get the value of $this->headers
     *
     * @return array The value of $this->headers
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Simplistically get the first Accept header
     * sent with the request, or null
     *
     * @return array | null The first Accept header of the request
     */
    public function getAcceptHeader()
    {
        return ($this->getAcceptHeaders())[0] ?? null;
    }

    /**
     * Simplistically get an array of Accept headers
     *
     * @return array An array of Accept headers
     */
    public function getAcceptHeaders(): array
    {
        return array_filter(
            array_map(
                function ($x) {
                    return trim(explode(';', $x)[0]) ?? null;
                },
                explode(',', $this->getHeader('Accept'))
            ) ?? []
        );
    }

    /**
     * Get the value of $this->httpMethod;
     * the http method given by the request
     *
     * @return array The value of $this->httpMethod
     */
    public function getHttpMethod()
    {
        return $this->httpMethod;
    }

    /**
     * Get the value of $this->uriPath;
     * the path of the request's uri
     *
     * @return array The value of $this->uriPath
     */
    public function getUriPath()
    {
        return $this->uriPath;
    }

    /**
     * Retrieve a header value by key, if it exists
     *
     * @return mixed The value of $this->headers
     */
    public function getHeader(string $key)
    {
        return $this->ucHeaders[strtoupper($key)] ?? null;
    }

    /**
     * Get the value of $this->ucHeaders
     *
     * @return array The value of $this->ucHeaders
     */
    public function getUcHeaders(): array
    {
        return $this->ucHeaders;
    }

    /**
     * Get a BasicAuthCredential, if one is found
     * in the request's headers.
     *
     * @return ApiMetal\Auth\BasicAuth\BasicAuthCredential|null  A BasicAuthCredential,
     *                                                    if one is found, or null
     */
    public function getBasicAuthCredential()
    {
        $basicAuthString = $this->headers['Authorization'] ?? '';

        return BasicAuth::isBasicAuthString($basicAuthString)
            ? BasicAuth::getCredentialFromBasicAuthString($basicAuthString)
            : null;
    }

    /**
     * Return the value of $this->params[$key]
     *
     * @param  string $key  The key whose value to return from $this->params
     * @return mixed        The value of $this->params[$key]
     */
    public function getParam(string $key)
    {
        return $this->params[$key] ?? null;
    }

    /**
     * Get the value of $this->params
     *
     * @return array The value of $this->params
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Set the value of $this->params to a merged array
     * of a variable number of arguments
     *
     * @param array $params     A k/v array of params
     * @param array $moreParams An array of n k/v param arrays
     * @return ApiMetal\Request\Request  This request instance
     */
    public function setParams(array $params = [], ...$moreParams): Request
    {
        if (!empty($moreParams)) {
            $moreParams = array_reduce(
                $moreParams,
                function ($carry, $item) { return array_merge($carry, $item); },
                []
            );
            $params = array_merge($params, $moreParams);
        }

        $this->params = $params;

        return $this;
    }

    /**
     * Set the value of $this->params[$key] to $value
     *
     * @param string $key   The key in $this->params to set
     * @param mixed  $value The value to set
     * @return ApiMetal\Request\Request  This request instance
     */
    public function setParam(string $key, $value): Request
    {
        $this->params[$key] = $value;

        return $this;
    }

    // PROTECTED

    /**
     * Set the value of $this->ucHeaders, to the value
     * of $this->headers with keys transformed to ucase
     *
     * @return ApiMetal\Request\Request  This request instance
     */
    protected function setUcHeaders(): Request
    {
        $headers = $this->getHeaders();
        $this->ucHeaders = array_change_key_case($headers, CASE_UPPER);

        return $this;
    }

    /**
     * Return the value of $this->server['REQUEST_METHOD']
     *
     * @return mixed The value of $this->server['REQUEST_METHOD']
     */
    protected function initHttpMethod()
    {
        return $this->server['REQUEST_METHOD'] ?? null;
    }

    /**
     * Return the value of $this->server['REQUEST_URI']
     * before any query parameters
     *
     * @return string The value of $this->server['REQUEST_URI'] before query params
     */
    protected function initUriPath(): string
    {
        $uri = $this->server['REQUEST_URI'] ?? '';

        // Strip query string (?foo=bar) and decode URI
        $uri = (false !== $pos = strpos($uri, '?'))
            ? substr($uri, 0, $pos)
            : $uri;

        return rawurldecode($uri);
    }

    /**
     * Return the value of apache_request_headers(),
     * if the function exists, else the value of
     * self::cli_request_headers(), a proxy based on $this->headers
     *
     * @return array Request headers as identified by the first available method
     */
    protected function initHeaders(): array
    {
        return function_exists('apache_request_headers')
            // @codeCoverageIgnoreStart
            ? \apache_request_headers()
            // @codeCoverageIgnoreEnd
            : self::cli_request_headers();
    }

    /**
     * Return an array of perceived request headers
     * as extracted from $this->server, used in the
     * absence of apache_request_headers, ie, non-apache requests.
     *
     * @return array An array of perceived request headers
     */
    protected function cli_request_headers(): array
    {
        $arh = [];
        $rx_http = '/\AHTTP_/';
        foreach ($this->server as $key => $val) {
            if (preg_match($rx_http, $key)) {
                $arh_key = preg_replace($rx_http, '', $key);
                $rx_matches = [];
                $rx_matches = explode('_', $arh_key);
                if (count($rx_matches) > 0 and strlen($arh_key) > 2) {
                    foreach ($rx_matches as $ak_key => $ak_val) {
                        $rx_matches[$ak_key] = ucfirst($ak_val);
                    }
                    $arh_key = implode('-', $rx_matches);
                    $arh_key2 = implode('_', $rx_matches);
                }
                $arh[$arh_key] = $val;
                $arh[$arh_key2] = $val;
            }
        }

        return $arh;
    }

    /**
     * Get the complete url used for this request
     *
     * @return string
     */
    public function getUrl()
    {
        $server = $this->getServer();

        $url = (
            (array_key_exists('HTTPS', $server) && $server['HTTPS'] == 'on')
            || (array_key_exists('HTTP_X_FORWARDED_PROTO', $server) && $server['HTTP_X_FORWARDED_PROTO'] == 'https')
        )
            ? 'https://'
            : 'http://';

        $url .= $server['SERVER_NAME'];

        if ($server['SERVER_PORT'] != '80' && $server['SERVER_PORT'] != '443') {
            $url .= ':' . $server['SERVER_PORT'];
        }

        $url .= $server['REQUEST_URI'];

        return $url;
    }
}
