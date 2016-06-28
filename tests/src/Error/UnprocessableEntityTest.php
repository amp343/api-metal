<?php

namespace ApiMetal\Tests\Error;

use ApiMetal\Error\UnprocessableEntity;
use ApiMetal\Tests\TestCase;

class UnprocessableEntityTest extends TestCase
{
    /**
     * @test
     */
    public function throwing_UnprocessableEntity_with_no_arguments_should_result_in_an_exception_with_code_422_and_message_unprocessable_entity()
    {
        try {
            throw new UnprocessableEntity();
            $this->assertTrue(false);
        } catch (UnprocessableEntity $e) {
            $this->assertEquals($e->getCode(), 422);
            $this->assertEquals($e->getMessage(), 'Unprocessable entity');
        }
    }

    /**
     * @test
     */
    public function throwing_UnprocessableEntity_with_a_message_should_result_in_an_exception_with_code_422_and_that_message()
    {
        $expected = 'expected';

        try {
            throw new UnprocessableEntity($expected);
            $this->assertTrue(false);
        } catch (UnprocessableEntity $e) {
            $this->assertEquals($e->getCode(), 422);
            $this->assertEquals($e->getMessage(), $expected);
        }
    }
}
