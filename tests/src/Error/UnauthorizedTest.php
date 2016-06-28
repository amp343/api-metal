<?php

namespace ApiMetal\Tests\Error;

use ApiMetal\Error\Unauthorized;
use ApiMetal\Tests\TestCase;

class UnauthorizedTest extends TestCase
{
    /**
     * @test
     */
    public function throwing_Unauthorized_with_no_arguments_should_result_in_an_exception_with_code_401_and_message_unauthorized()
    {
        try {
            throw new Unauthorized();
            $this->assertTrue(false);
        } catch (Unauthorized $e) {
            $this->assertEquals($e->getCode(), 401);
            $this->assertEquals($e->getMessage(), 'Unauthorized');
        }
    }

    /**
     * @test
     */
    public function throwing_Unauthorized_with_a_message_should_result_in_an_exception_with_code_401_and_that_message()
    {
        $expected = 'expected';

        try {
            throw new Unauthorized($expected);
            $this->assertTrue(false);
        } catch (Unauthorized $e) {
            $this->assertEquals($e->getCode(), 401);
            $this->assertEquals($e->getMessage(), $expected);
        }
    }
}
