<?php

namespace ApiMetal\Response;

use ApiMetal\Util\Xml;

class Response
{
    /**
     * The response body
     *
     * @var string
     */
    protected $body = '';

    /**
     * The response status
     *
     * @var integer
     */
    protected $status = 200;

    /**
     * The response format
     *
     * @var string
     */
    protected $format = 'json';

    /**
     * @codeCoverageIgnore
     *
     * Respond; that is, is, set the requisite headers,
     * and echo the response body
     */
    public function respond()
    {
        http_response_code($this->getStatus());
        header('Content-Type: application/' . $this->getFormat() . '; charset=UTF-8');
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: -1');

        $body = $this->getBodyAs($this->getFormat()) ?? '';

        echo $body;
    }

    /**
     * Set the value of $this->format
     *
     * @param string $format The format of the response body ('xml', 'json)
     * @return ApiMetal\Response\Response    This response instance
     */
    public function setFormat(string $format): Response
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Set the value of $this->body
     *
     * @param string $body The response body
     * @return ApiMetal\Response\Response    This response instance
     */
    public function setBody($body): Response
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Set the value of $this->status
     *
     * @param int $status The response status
     * @return ApiMetal\Response\Response    This response instance
     */
    public function setStatus(int $status): Response
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Set the value of $this->status and $this->format
     * according to the given Exception ->code and ->message
     *
     * @param \Exception $exception Some exception by which to affect
     *                              the response status and body
     * @return ApiMetal\Response\Response    This response instance
     */
    public function setError(\Exception $exception): Response
    {
        $this->status = $exception->getCode() > 0
            ? $exception->getCode()
            : 500;
        $this->body = $this->buildErrorMessageJson($exception);

        return $this;
    }

    /**
     * Given an Exception, return an error message JSON
     * according to JSON API spec; ie, an array with key
     * 'errors' having elements with keys:
     *  - status:   the intended HTTP status
     *  - detail:   the error message text
     *  - code:     an internal, vendor-specific error code
     *
     * @param  Exception $e [description]
     * @return array        The resulting error message
     */
    public function buildErrorMessageJson(\Exception $e): array
    {
        return ['errors' => [
            [
                'status' => $e->getCode(),
                'detail' => $e->getMessage(),
                'code' => method_exists($e, 'getInternalCode') ? $e->getInternalCode() : 0
            ]
        ]];
    }

    /**
     * Get the value of $this->body
     *
     * @return mixed The value of $this->body
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Get the value of $this->status
     *
     * @return int The value of $this->status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Get the value of $this->format
     *
     * @return string The value of $this->format
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * Return this body in the requested format
     *
     * @param  string $format The requested format ('xml', 'json')
     * @return string         The response body string in the requested format
     */
    public function getBodyAs(string $format = null): string
    {
        $format = $format ?? $this->getFormat();

        switch ($format) {
            case 'json':
                $body = $this->getBodyJson();
                break;
            case 'xml':
                $body = $this->getBodyXml();
                break;
        }

        return $body;
    }

    /**
     * Get the value of $this->body as a json string
     *
     * @return string $this->body as a json string
     */
    public function getBodyJson()
    {
        return json_encode($this->getBody());
    }

    /**
     * Get the value of $this->body as an xml string
     *
     * @return string $this->body as an xml string
     */
    public function getBodyXml()
    {
        $body = $this->getBody();

        return Xml::fromJsonObj($body, 'response', true);
    }
}
