<?php

namespace ApiMetal\Tests\Error;

use ApiMetal\Error\ServiceUnavailable;
use ApiMetal\Tests\TestCase;

class ServiceUnavailableTest extends TestCase
{
    /**
     * @test
     */
    public function throwing_ServiceUnavailable_with_no_arguments_should_result_in_an_exception_with_code_503_and_message_service_unavailable()
    {
        try {
            throw new ServiceUnavailable();
            $this->assertTrue(false);
        } catch (ServiceUnavailable $e) {
            $this->assertEquals($e->getCode(), 503);
            $this->assertEquals($e->getMessage(), 'Service unavailable');
        }
    }

    /**
     * @test
     */
    public function throwing_ServiceUnavailable_with_a_message_should_result_in_an_exception_with_code_503_and_that_message()
    {
        $expected = 'expected';

        try {
            throw new ServiceUnavailable($expected);
            $this->assertTrue(false);
        } catch (ServiceUnavailable $e) {
            $this->assertEquals($e->getCode(), 503);
            $this->assertEquals($e->getMessage(), $expected);
        }
    }
}
