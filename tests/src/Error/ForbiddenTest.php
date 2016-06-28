<?php

namespace ApiMetal\Tests\Error;

use ApiMetal\Error\Forbidden;
use ApiMetal\Tests\TestCase;

class ForbiddenTest extends TestCase
{
    /**
     * @test
     */
    public function throwing_Forbidden_with_no_arguments_should_result_in_an_exception_with_code_403_and_message_forbidden()
    {
        try {
            throw new Forbidden();
            $this->assertTrue(false);
        } catch (Forbidden $e) {
            $this->assertEquals($e->getCode(), 403);
            $this->assertEquals($e->getMessage(), 'Forbidden');
        }
    }

    /**
     * @test
     */
    public function throwing_Forbidden_with_a_message_should_result_in_an_exception_with_code_403_and_that_message()
    {
        $expected = 'expected';

        try {
            throw new Forbidden($expected);
            $this->assertTrue(false);
        } catch (Forbidden $e) {
            $this->assertEquals($e->getCode(), 403);
            $this->assertEquals($e->getMessage(), $expected);
        }
    }
}
