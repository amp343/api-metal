<?php

namespace ApiMetal\Tests\Error;

use ApiMetal\Error\MethodNotAllowed;
use ApiMetal\Tests\TestCase;

class MethodNotAllowedTest extends TestCase
{
    /**
     * @test
     */
    public function throwing_MethodNotAllowed_with_no_arguments_should_result_in_an_exception_with_code_405_and_message_method_not_allowed()
    {
        try {
            throw new MethodNotAllowed();
            $this->assertTrue(false);
        } catch (MethodNotAllowed $e) {
            $this->assertEquals($e->getCode(), 405);
            $this->assertEquals($e->getMessage(), 'Method not allowed');
        }
    }

    /**
     * @test
     */
    public function throwing_MethodNotAllowed_with_a_message_should_result_in_an_exception_with_code_405_and_that_message()
    {
        $expected = 'expected';

        try {
            throw new MethodNotAllowed($expected);
            $this->assertTrue(false);
        } catch (MethodNotAllowed $e) {
            $this->assertEquals($e->getCode(), 405);
            $this->assertEquals($e->getMessage(), $expected);
        }
    }
}
