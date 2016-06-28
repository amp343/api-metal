<?php

namespace ApiMetal\Tests\Controller\Validation;

use ApiMetal\Controller\Validation\ValidatorFactory;

class ValidatorFactoryTest extends \ApiMetal\Tests\TestCase
{
    /**
     * @test
     */
    public function getValidatorByType_should_return_an_array_with_keys_validator_and_errors()
    {
        $result = ValidatorFactory::getValidatorByType();

        $this->assertTrue(array_key_exists('validator', $result));
        $this->assertTrue(array_key_exists('errors', $result));
    }

    /**
     * @test
     */
    public function getValidatorByType_should_return_an_array_with_keys_filled_when_the_given_type_matches_a_validator()
    {
        $result = ValidatorFactory::getValidatorByType('nonZeroInt');

        $this->assertInstanceOf('Respect\Validation\Validator', $result['validator']);
        $this->assertTrue(is_array($result['errors']));
    }

    /**
     * @test
     */
    public function getValidatorByType_should_return_the_correct_validator_for_type_string()
    {
        $result = ValidatorFactory::getValidatorByType('string');

        $this->assertTrue($result['validator']->validate('abc'));
        $this->assertFalse($result['validator']->validate(1));
    }

    /**
     * @test
     */
    public function getValidatorByType_should_return_the_correct_validator_for_type_integer()
    {
        $result = ValidatorFactory::getValidatorByType('integer');

        $this->assertTrue($result['validator']->validate(1));
        $this->assertFalse($result['validator']->validate(1.44));
    }

    /**
     * @test
     */
    public function getValidatorByType_should_return_the_correct_validator_for_type_int()
    {
        $result = ValidatorFactory::getValidatorByType('int');

        $this->assertTrue($result['validator']->validate(1));
        $this->assertFalse($result['validator']->validate(1.44));
    }

    /**
     * @test
     */
    public function getValidatorByType_should_return_the_correct_validator_for_type_latitude()
    {
        $result = ValidatorFactory::getValidatorByType('latitude');

        $this->assertTrue($result['validator']->validate(1));
        $this->assertFalse($result['validator']->validate(91));
        $this->assertFalse($result['validator']->validate(-91));
    }

    /**
     * @test
     */
    public function getValidatorByType_should_return_the_correct_validator_for_type_longitude()
    {
        $result = ValidatorFactory::getValidatorByType('longitude');

        $this->assertTrue($result['validator']->validate(1));
        $this->assertFalse($result['validator']->validate(181));
        $this->assertFalse($result['validator']->validate(-181));
    }

    /**
     * @test
     */
    public function getValidatorByType_should_return_the_correct_validator_for_type_flag()
    {
        $result = ValidatorFactory::getValidatorByType('flag');

        $this->assertTrue($result['validator']->validate(1));
        $this->assertTrue($result['validator']->validate(0));
        $this->assertFalse($result['validator']->validate(-1));
        $this->assertFalse($result['validator']->validate(true));
        $this->assertFalse($result['validator']->validate(false));
    }

    /**
     * @test
     */
    public function getValidatorByType_should_return_the_correct_validator_for_type_positiveNonZeroInt()
    {
        $result = ValidatorFactory::getValidatorByType('positiveNonZeroInt');

        $this->assertTrue($result['validator']->validate(100));
        $this->assertFalse($result['validator']->validate(0));
        $this->assertFalse($result['validator']->validate(-1));
        $this->assertFalse($result['validator']->validate(100.1));
    }

    /**
     * @test
     */
    public function getValidatorByType_should_return_the_correct_validator_for_type_nonNegativeInt()
    {
        $result = ValidatorFactory::getValidatorByType('nonNegativeInt');

        $this->assertTrue($result['validator']->validate(1));
        $this->assertTrue($result['validator']->validate(0));
        $this->assertFalse($result['validator']->validate(-1));
        $this->assertFalse($result['validator']->validate(100.1));
    }

    /**
     * @test
     */
    public function getValidatorByType_should_return_the_correct_validator_for_type_Y_m_d()
    {
        $result = ValidatorFactory::getValidatorByType('Y-m-d');

        $this->assertTrue($result['validator']->validate('2014-08-08'));
        $this->assertFalse($result['validator']->validate('08-08-2014'));
    }

    /**
     * @test
     */
    public function getValidatorByType_should_return_the_correct_validator_for_type_nonZeroInt()
    {
        $result = ValidatorFactory::getValidatorByType('nonZeroInt');

        $this->assertTrue($result['validator']->validate(1));
        $this->assertTrue($result['validator']->validate(-1));
        $this->assertFalse($result['validator']->validate(0));
        $this->assertFalse($result['validator']->validate(1.2));

        // this validator has custom errors
        $this->assertNotEmpty($result['errors']);
    }

    /**
     * @test
     */
    public function getValidatorByType_should_return_the_correct_validator_for_type_phone()
    {
        $result = ValidatorFactory::getValidatorByType('phone');

        $this->assertTrue($result['validator']->validate(15555555555));
        $this->assertTrue($result['validator']->validate('1-603-549-2944'));
        $this->assertFalse($result['validator']->validate(0));
        $this->assertFalse($result['validator']->validate('abcdefghijk'));
    }
}
