<?php

namespace ApiMetal\Tests\Error;

use ApiMetal\Error\InternalServerError;
use ApiMetal\Tests\TestCase;

class InternalServerErrorTest extends TestCase
{
    /**
     * @test
     */
    public function throwing_InternalServerError_with_no_arguments_should_result_in_an_exception_with_code_500_and_message_internal_server_error()
    {
        try {
            throw new InternalServerError();
            $this->assertTrue(false);
        } catch (InternalServerError $e) {
            $this->assertEquals($e->getCode(), 500);
            $this->assertEquals($e->getMessage(), 'Internal server error');
        }
    }

    /**
     * @test
     */
    public function throwing_InternalServerError_with_a_message_should_result_in_an_exception_with_code_500_and_that_message()
    {
        $expected = 'expected';

        try {
            throw new InternalServerError($expected);
            $this->assertTrue(false);
        } catch (InternalServerError $e) {
            $this->assertEquals($e->getCode(), 500);
            $this->assertEquals($e->getMessage(), $expected);
        }
    }
}
