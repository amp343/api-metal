<?php

namespace ApiMetal\Tests\Response;

use ApiMetal\Response\Response;
use ApiMetal\Tests\TestCase;
use ApiMetal\Tests\TestThing;
use ApiMetal\Util\Xml;

class ResponseTest extends TestCase
{
    public function getClassName()
    {
        return '\\ApiMetal\\Response\\Response';
    }

    /**
     * @test
     */
    public function setFormat_should_set_the_responseFormat_property_and_return_Response()
    {
        $response = new Response;
        $expected = 'someResponseFormat';

        $result = $response->setFormat($expected);

        $this->assertInstanceOf($this->getClassName(), $result);
        $this->assertEquals($response->getFormat(), $expected);
    }

    /**
     * @test
     */
    public function setFormat_should_throw_exception_if_argument_is_not_a_string()
    {
        $response = new Response;
        $expected = [];

        $this->assertTypeError(function () use ($response, $expected) {
            return $response->setFormat($expected);
        });
    }

    /**
     * @test
     */
    public function setBody_should_set_the_body_property_and_return_Response()
    {
        $response = new Response;
        $expected = 'someResponseBody';

        $result = $response->setBody($expected);

        $this->assertInstanceOf($this->getClassName(), $result);
        $this->assertEquals($response->getBody(), $expected);
    }

    /**
     * @test
     */
    public function setBody_should_set_the_body_property_if_body_is_an_array_and_return_Response()
    {
        $response = new Response;

        $body = [
            new TestThing('a'),
            new TestThing('b'),
            new TestThing('c'),
        ];
        $expected = $body;

        $result = $response->setBody($body);
        $this->assertEquals($response->getBody(), $expected);
    }

    /**
     * @test
     */
    public function setStatus_should_set_the_status_property_and_return_Response()
    {
        $response = new Response;
        $expected = 404;

        $result = $response->setStatus($expected);

        $this->assertInstanceOf($this->getClassName(), $result);
        $this->assertEquals($response->getStatus(), $expected);
    }

    /**
     * @test
     */
    public function setStatus_should_throw_exception_if_argument_is_not_an_int()
    {
        $response = new Response;
        $expected = [];

        $this->assertTypeError(function () use ($response, $expected) {
            return $response->setStatus($expected);
        });
    }

    /**
     * @test
     */
    public function setError_should_return_an_instance_of_Response()
    {
        $response = new Response;
        $exception = new \Exception;

        $result = $response->setError($exception);

        $this->assertInstanceOf($this->getClassName(), $result);
    }

    /**
     * @test
     */
    public function setError_should_throw_exception_if_argument_is_not_an_Error()
    {
        $response = new Response;
        $expected = [];

        $this->assertTypeError(function () use ($response, $expected) {
            return $response->setError($expected);
        });
    }

    /**
     * @test
     */
    public function setError_should_set_this_status_to_Exception_getCode()
    {
        $response = new Response;
        $expected = 422;

        $exception = new \Exception('message', $expected);
        $response->setError($exception);

        $this->assertEquals($response->getStatus(), $expected);
    }

    /**
     * @disabled_test
     * TODO:: setError sets the body property to the value of buildErrorMessageJson
     */
    public function setError_should_set_the_message_property_to_the_value_of_Exception_getMessage()
    {
        $response = new Response;
        $expected = 'some message';

        $exception = new \Exception($expected);
        $response->setError($exception);

        $this->assertEquals($response->getMessage(), $expected);
    }

    /**
     * @test
     * TODO: change description
     */
     public function getBodyJson_should_return_an_json_object_having_status_message_and_body_as_a_string()
     {
         $status = 200;
         $body = ['abc' => 'def'];
         $expected = json_encode($body);

         $response = (new Response)
            ->setStatus(200)
            ->setBody($body);

         $this->assertEquals($response->getBodyJson(), $expected);
     }

    /**
     * @test
     * TODO: change description
     */
    public function getBodyXml_should_return_an_xml_string_having_nodes_status_message_and_body()
    {
        $status = 200;
        $body = ['abc' => 'def'];
        $expected = $this->buildXmlResponse($body);

        $response = (new Response)
            ->setStatus($status)
            ->setBody($body);

        $this->assertEquals($response->getBodyXml(), $expected);
    }

    /**
     * @test
     */
    public function getBodyAs_should_return_an_xml_string_when_argument_is_xml()
    {
        $status = 200;
        $body = ['abc' => 'def'];
        $expected = $this->buildXmlResponse($body);

        $response = (new Response)
            ->setStatus($status)
            ->setBody($body);

        $this->assertEquals($response->getBodyAs('xml'), $expected);
    }

    /**
     * @test
     */
    public function getBodyAs_should_return_an_xml_string_when_Response_getFormat_is_xml()
    {
        $status = 200;
        $body = ['abc' => 'def'];

        $response = (new Response)
            ->setStatus($status)
            ->setBody($body)
            ->setFormat('xml');

        $expected = $this->buildXmlResponse($body);

        $this->assertEquals($response->getBodyAs(), $expected);
    }

    /**
     * @test
     */
    public function getBodyAs_should_return_a_json_string_when_argument_is_json()
    {
        $status = 200;
        $body = ['abc' => 'def'];
        $expected = json_encode($body);

        $response = (new Response)
            ->setStatus($status)
            ->setBody($body);

        $this->assertEquals($response->getBodyAs('json'), $expected);
    }

    /**
     * @test
     */
    public function getBodyAs_should_return_a_json_string_when_Response_getFormat_is_json()
    {
        $status = 200;
        $body = ['abc' => 'def'];
        $expected = json_encode($body);

        $response = (new Response)
            ->setStatus($status)
            ->setBody($body)
            ->setFormat('json');

        $this->assertEquals($response->getBodyAs(), $expected);
    }

    protected function buildXmlResponse($body)
    {
        return Xml::fromJsonObj($body, 'response', true);
    }
}
