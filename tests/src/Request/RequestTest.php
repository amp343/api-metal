<?php

namespace ApiMetal\Tests\Request;

use ApiMetal\Request\Request;
use ApiMetal\Tests\TestCase;

class RequestTest extends TestCase
{
    /**
     * @test
     */
    public function construct_should_throw_a_TypeError_if_argument_1_server_is_not_an_array()
    {
        $this->assertTypeError(function () {
            return new Request('abc');
        });
    }

    /**
     * @test
     */
    public function getHeaders_should_return_the_result_of_cli_request_headers_in_test_context()
    {
        $server = [
            'TEST_KEY_1' => 'a',
            'TEST_KEY_2' => 'b',
            'HTTP_TEST_KEY_3' => 'c',
            'HTTP_TEST_KEY_4' => 'd',
        ];

        $expected = [
            'TEST_KEY_3' => 'c',
            'TEST-KEY-3' => 'c',
            'TEST_KEY_4' => 'd',
            'TEST-KEY-4' => 'd'
        ];

        $request = new Request($server);
        $this->assertEquals($request->getHeaders(), $expected);
    }

    /**
     * @test
     */
    public function getServer_should_return_the_value_of_this_server()
    {
        $expected = ['a' => 'b', 'c' => 'd'];
        $request = new Request($expected);

        $this->assertEquals($request->getServer(), $expected);
    }

    /**
     * @test
     */
    public function getHttpMethod_should_return_the_value_of_this_httpMethod()
    {
        $server = ['REQUEST_METHOD' => 'PATCH'];
        $expected = 'PATCH';

        $request = new Request($server);
        $this->assertEquals($request->getHttpMethod(), $expected);
    }

    /**
     * @test
     */
    public function getUriPath_should_return_the_value_of_this_path()
    {
        $server = ['REQUEST_URI' => '/consumerAPI/public/getRestaurants?lat=40.5&lon=-79.5'];
        $expected = '/consumerAPI/public/getRestaurants';

        $request = new Request($server);
        $this->assertEquals($request->getUriPath(), $expected);
    }

    /**
     * @test
     */
    public function getBasicAuthCredential_should_return_a_BasicAuthCredential_from_the_header_having_key_Authorization()
    {
        $server = ['HTTP_Authorization' => 'Basic dXNlcjpwYXNzd29yZA=='];
        $request = new Request($server);
        $credential = $request->getBasicAuthCredential();

        $expectedUser = 'user';
        $expectedPassword = 'password';

        $this->assertEquals($credential->getUser(), $expectedUser);
        $this->assertEquals($credential->getPassword(), $expectedPassword);
        $this->assertInstanceOf('\\ApiMetal\\Auth\\BasicAuth\\BasicAuthCredential', $credential);
    }

    /**
     * @test
     */
    public function getBasicAuthCredential_should_return_null_if_the_value_of_the_Authorization_header_does_not_look_like_a_user_password_combo()
    {
        $server = ['HTTP_Authorization' => 'Basic dslkadsjadsf;kafd'];
        $request = new Request($server);
        $credential = $request->getBasicAuthCredential();

        $this->assertNull($credential);
    }

    /**
     * @test
     */
    public function getAcceptHeaders_should_return_the_array_of_Accept_headers_when_they_are_set()
    {
        $server = ['HTTP_Accept' => 'text/html; level=1, application/json'];
        $request = new Request($server);
        $acceptHeaders = $request->getAcceptHeaders();

        $expected = ['text/html', 'application/json'];

        $this->assertEquals($request->getAcceptHeaders(), $expected);
    }

    /**
     * @test
     */
    public function getAcceptHeaders_should_return_an_empty_array_when_they_are_not_set()
    {
        $this->assertEquals((new Request)->getAcceptHeaders(), []);
    }

    /**
     * @test
     */
    public function getAcceptHeader_should_return_the_first_accept_header_if_they_are_set()
    {
        $server = ['HTTP_Accept' => 'text/html; level=1, application/json'];
        $request = new Request($server);
        $expected = 'text/html';

        $this->assertEquals($request->getAcceptHeader(), $expected);
    }

    /**
     * @test
     */
    public function getAcceptHeader_should_return_null_if_none_is_found()
    {
        $server = ['HTTP_Acceptzzz' => 'text/html; level=1, application/json'];
        $request = new Request($server);

        $this->assertNull($request->getAcceptHeader());
    }

    /**
     * @test
     */
    public function setUcHeaders_should_take_the_request_headers_then_transform_the_case_to_upper_and_store_as_this_ucHeaders()
    {
        $server = [
            'HTTP_some-thing' => 'a',
            'HTTP_SOME-other_THING' => 'b'
        ];

        $expected = [
            'SOME-THING' => 'a',
            'SOME-OTHER-THING' => 'b',
            'SOME-OTHER_THING' => 'b'
        ];

        $request = new Request($server);

        $this->assertEquals($request->getUcHeaders(), $expected);
        $this->assertEquals($request->getHeader('some-thing'), 'a');
        $this->assertEquals($request->getHeader('some-other-thing'), 'b');
        $this->assertEquals($request->getHeader('SOME-OTHER-THING'), 'b');
    }

    /**
     * @test
     */
    public function setParam_should_set_the_given_key_value_pair_in_this_params_and_return_Request()
    {
        $request = new Request([]);
        $result = $request->setParam('a', 'b');

        $this->assertEquals($request->getParam('a'), 'b');
        $this->assertInstanceOf('\\ApiMetal\\Request\\Request', $result);
    }

    /**
     * @test
     */
    public function getUrl_should_extract_the_port_correctly_when_it_is_not_80_or_443()
    {
        $request = new Request([
            'SERVER_NAME' => 'fake.com',
            'SERVER_PORT' => 81,
            'REQUEST_URI' => '/something?key=value',
            'HTTP_X_TWILIO_SIGNATURE' => 'signature'
        ]);

        $expected = 'http://fake.com:81/something?key=value';

        $this->assertEquals($request->getUrl(), $expected);
    }

    /**
     * @test
     */
    public function getUrl_should_get_the_correct_url_when_protocol_is_https()
    {
        $request = new Request([
            'HTTPS' => 'on',
            'SERVER_NAME' => 'fake.com',
            'SERVER_PORT' => 81,
            'REQUEST_URI' => '/something?key=value',
            'HTTP_X_TWILIO_SIGNATURE' => 'signature'
        ]);

        $expected = 'https://fake.com:81/something?key=value';

        $this->assertEquals($request->getUrl(), $expected);
    }
}
