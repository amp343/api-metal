<?php

namespace ApiMetal\Tests\Error;

use ApiMetal\Error\BadRequest;
use ApiMetal\Tests\TestCase;

class BadRequestTest extends TestCase
{
    /**
     * @test
     */
    public function throwing_BadRequest_with_no_arguments_should_result_in_an_exception_with_code_400_and_message_bad_request()
    {
        try {
            throw new BadRequest();
            $this->assertTrue(false);
        } catch (BadRequest $e) {
            $this->assertEquals($e->getCode(), 400);
            $this->assertEquals($e->getMessage(), 'Bad request');
        }
    }

    /**
     * @test
     */
    public function throwing_BadRequest_with_a_message_should_result_in_an_exception_with_code_400_and_that_message()
    {
        $expected = 'expected';

        try {
            throw new BadRequest($expected);
            $this->assertTrue(false);
        } catch (BadRequest $e) {
            $this->assertEquals($e->getCode(), 400);
            $this->assertEquals($e->getMessage(), $expected);
        }
    }
}
