<?php

namespace ApiMetal\Auth\Tests\BasicAuth;

use ApiMetal\Auth\BasicAuth\BasicAuthCredential;
use ApiMetal\Tests\TestCase;

class BasicAuthCredentialTest extends TestCase
{
    /**
     * @test
     */
    public function construct_should_throw_a_TypeError_if_argument_1_user_is_not_a_string()
    {
        $this->assertTypeError(function () {
            return new BasicAuthCredential([], '');
        });
    }

    /**
     * @test
     */
    public function construct_should_throw_a_TypeError_if_argument_2_password_is_not_a_string()
    {
        $this->assertTypeError(function () {
            return new BasicAuthCredential('', []);
        });
    }

    /**
     * @test
     */
    public function getUser_should_return_the_value_of_this_user()
    {
        $expected = 'user';
        $credential = new BasicAuthCredential($expected, '');

        $this->assertEquals($credential->getUser(), $expected);
    }

    /**
     * @test
     */
    public function getPassword_should_return_the_value_of_this_password()
    {
        $expected = 'password';
        $credential = new BasicAuthCredential('', $expected);

        $this->assertEquals($credential->getPassword(), $expected);
    }

    /**
     * @test
     */
    public function toFlatArray_should_return_this_user_and_password_as_a_0_1_array()
    {
        $expected = ['user', 'password'];
        $credential = new BasicAuthCredential($expected[0], $expected[1]);

        $this->assertEquals($credential->toFlatArray(), $expected);
    }

    /**
     * @test
     */
    public function toArray_should_return_this_as_a_kv_array_with_keys_user_and_password()
    {
        $expected = ['user' => 'user', 'password' => 'password'];
        $credential = new BasicAuthCredential($expected['user'], $expected['password']);

        $this->assertEquals($credential->toArray(), $expected);
    }

    /**
     * @test
     */
    public function isMatch_should_return_true_when_user_and_password_match()
    {
        $expected = ['user' => 'user', 'password' => 'password'];
        $credentialA = new BasicAuthCredential($expected['user'], $expected['password']);
        $credentialB = new BasicAuthCredential($expected['user'], $expected['password']);

        $this->assertTrue(BasicAuthCredential::isMatch($credentialA, $credentialB));
    }

    /**
     * @test
     */
    public function isMatch_should_return_false_when_password_doesnt_match()
    {
        $expected = ['user' => 'user', 'password' => 'password'];
        $credentialA = new BasicAuthCredential($expected['user'], 'badPassword');
        $credentialB = new BasicAuthCredential($expected['user'], $expected['password']);

        $this->assertFalse(BasicAuthCredential::isMatch($credentialA, $credentialB));
    }

    /**
     * @test
     */
    public function isMatch_should_return_false_when_user_doesnt_match()
    {
        $expected = ['user' => 'user', 'password' => 'password'];
        $credentialA = new BasicAuthCredential('badUser', $expected['password']);
        $credentialB = new BasicAuthCredential($expected['user'], $expected['password']);

        $this->assertFalse(BasicAuthCredential::isMatch($credentialA, $credentialB));
    }

    /**
     * @test
     */
    public function isMatch_should_return_false_when_neither_user_nor_password_match()
    {
        $expected = ['user' => 'user', 'password' => 'password'];
        $credentialA = new BasicAuthCredential('badUser', 'badPassword');
        $credentialB = new BasicAuthCredential($expected['user'], $expected['password']);

        $this->assertFalse(BasicAuthCredential::isMatch($credentialA, $credentialB));
    }

    /**
     * @test
     */
    public function isMatch_should_throw_a_TypeError_if_argument_1_credentialA_is_not_a_BasicAuthCredential()
    {
        $this->assertTypeError(function () {
            $credential = new BasicAuthCredential('', '');
            return BasicAuthCredential::isMatch([], $credential);
        });
    }

    /**
     * @test
     */
    public function isMatch_should_throw_a_TypeError_if_argument_2_credentialB_is_not_a_BasicAuthCredential()
    {
        $this->assertTypeError(function () {
            $credential = new BasicAuthCredential('', '');
            return BasicAuthCredential::isMatch($credential, []);
        });
    }

    /**
     * @test
     */
    public function getEncodedCredential_should_return_the_expected_encoded_credential_string()
    {
        $user = 'user';
        $password = 'password';
        $expected = base64_encode("{$user}:{$password}");

        $this->assertEquals((new BasicAuthCredential($user, $password))->getEncodedCredential(), $expected);
    }

    /**
     * @test
     */
    public function getEncodedHeaderValue_should_return_the_encoded_credential_string_with_the_Basic_prefix()
    {
        $user = 'user';
        $password = 'password';
        $expected = "Basic " . base64_encode("{$user}:{$password}");

        $this->assertEquals((new BasicAuthCredential($user, $password))->getEncodedHeaderValue(), $expected);
    }

    /**
     * @test
     */
    public function getEncodedHeader_should_return_an_array_with_key_Authorization_and_value_equal_to_the_encoded_basic_auth_credential_string()
    {
        $user = 'user';
        $password = 'password';
        $expected = ['Authorization' => "Basic " . base64_encode("{$user}:{$password}")];

        $this->assertEquals((new BasicAuthCredential($user, $password))->getEncodedHeader(), $expected);
    }
}
