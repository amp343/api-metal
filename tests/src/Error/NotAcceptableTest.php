<?php

namespace ApiMetal\Tests\Error;

use ApiMetal\Error\NotAcceptable;
use ApiMetal\Tests\TestCase;

class NotAcceptableTest extends TestCase
{
    /**
     * @test
     */
    public function throwing_NotAcceptable_with_no_arguments_should_result_in_an_exception_with_code_406_and_message_not_acceptable()
    {
        try {
            throw new NotAcceptable();
            $this->assertTrue(false);
        } catch (NotAcceptable $e) {
            $this->assertEquals($e->getCode(), 406);
            $this->assertEquals($e->getMessage(), 'Not acceptable');
        }
    }

    /**
     * @test
     */
    public function throwing_NotAcceptable_with_a_message_should_result_in_an_exception_with_code_406_and_that_message()
    {
        $expected = 'expected';

        try {
            throw new NotAcceptable($expected);
            $this->assertTrue(false);
        } catch (NotAcceptable $e) {
            $this->assertEquals($e->getCode(), 406);
            $this->assertEquals($e->getMessage(), $expected);
        }
    }
}
