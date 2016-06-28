<?php

namespace ApiMetal\Auth\Tests\BasicAuth;

use ApiMetal\Auth\BasicAuth\BasicAuth;
use ApiMetal\Tests\TestCase;

class BasicAuthTest extends TestCase
{
    /**
     * @test
     */
    public function getCredentialFromBasicAuthString_should_throw_a_TypeError_if_argument_1_basicAuthString_is_not_a_string()
    {
        $this->assertTypeError(function () {
            return BasicAuth::getCredentialFromBasicAuthString([]);
        });
    }

    /**
     * @test
     */
    public function getCredentialFromBasicAuthString_should_take_a_string_and_return_a_BasicAuthCredential()
    {
        $encodedString = 'Y29uc3VtZXJhcGk6enpXVXdycUJzSlh4VHNqQ0RGUzIzVXNZMzU5YXJFczVEeVNheklB';
        $credential = BasicAuth::getCredentialFromBasicAuthString($encodedString);

        $expectedUser = 'consumerapi';
        $expectedPassword = 'zzWUwrqBsJXxTsjCDFS23UsY359arEs5DySazIA';

        $this->assertEquals($credential->getUser(), $expectedUser);
        $this->assertEquals($credential->getPassword(), $expectedPassword);
        $this->assertInstanceOf('\\ApiMetal\\Auth\\BasicAuth\\BasicAuthCredential', $credential);
    }

    /**
     * @test
     */
    public function isBasicAuthString_should_throw_a_TypeError_if_argument_1_basicAuthString_is_not_a_string()
    {
        $this->assertTypeError(function () {
            return BasicAuth::isBasicAuthString([]);
        });
    }

    /**
     * @test
     */
    public function isBasicAuthString_should_return_false_when_the_given_string_is_not_a_basic_auth_string()
    {
        $this->assertFalse(BasicAuth::isBasicAuthString('not a basic auth string'));
    }

    /**
     * @test
     */
    public function isBasicAuthString_should_return_true_when_the_given_string_is_a_basic_auth_string()
    {
        $this->assertTrue(BasicAuth::isBasicAuthString('Basic: I\'m a basic auth string'));
    }
}
