<?php

namespace ApiMetal\Tests;

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

    protected function assertIsArrayOfClass(array $arr = [], string $className)
    {
        $this->assertTrue(is_array($arr));

        foreach ($arr as $a) {
            $this->assertInstanceOf($className, $a);
        }
    }
}

class TestThing
{
    public function __construct($string)
    {
        $this->string = $string;
    }

    public function __toString()
    {
        return $this->string;
    }
}

class ExceptionWithData extends \Exception
{
    public function __construct($data)
    {
        parent::__construct();
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }
}
