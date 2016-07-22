<?php

namespace ApiMetal\Auth\Tests;

class TestCase extends \PHPUnit_Framework_TestCase
{
    public function assertTypeError(callable $closure)
    {
        try {
            $closure();
            $this->assertTrue(false);
        } catch (\TypeError $e) {
            $this->assertTrue(true);
        }
    }
}
