<?php

namespace ApiMetal\Tests\Error;

use ApiMetal\Error\RequestTimeout;
use ApiMetal\Tests\TestCase;

class RequestTimeoutTest extends TestCase
{
    /**
     * @test
     */
    public function throwing_RequestTimeout_with_no_arguments_should_result_in_an_exception_with_code_408_and_message_forbidden()
    {
        try {
            throw new RequestTimeout();
            $this->assertTrue(false);
        } catch (RequestTimeout $e) {
            $this->assertEquals($e->getCode(), 408);
            $this->assertEquals($e->getMessage(), 'Request timeout');
        }
    }

    /**
     * @test
     */
    public function throwing_RequestTimeout_with_a_message_should_result_in_an_exception_with_code_408_and_that_message()
    {
        $expected = 'expected';

        try {
            throw new RequestTimeout($expected);
            $this->assertTrue(false);
        } catch (RequestTimeout $e) {
            $this->assertEquals($e->getCode(), 408);
            $this->assertEquals($e->getMessage(), $expected);
        }
    }
}
