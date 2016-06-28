<?php

namespace ApiMetal\Tests\Error;

use ApiMetal\Error\NotFound;
use ApiMetal\Tests\TestCase;

class NotFoundTest extends TestCase
{
    /**
     * @test
     */
    public function throwing_NotFound_with_no_arguments_should_result_in_an_exception_with_code_403_and_message_not_found()
    {
        try {
            throw new NotFound();
            $this->assertTrue(false);
        } catch (NotFound $e) {
            $this->assertEquals($e->getCode(), 404);
            $this->assertEquals($e->getMessage(), 'Not found');
        }
    }

    /**
     * @test
     */
    public function throwing_NotFound_with_a_message_should_result_in_an_exception_with_code_404_and_that_message()
    {
        $expected = 'expected';

        try {
            throw new NotFound($expected);
            $this->assertTrue(false);
        } catch (NotFound $e) {
            $this->assertEquals($e->getCode(), 404);
            $this->assertEquals($e->getMessage(), $expected);
        }
    }
}
