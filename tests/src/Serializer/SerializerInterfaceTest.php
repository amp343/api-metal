<?php

namespace ApiMetal\Tests\Serializer;

use ApiMetal\Serializer\SerializerInterface;
use ApiMetal\Tests\TestCase;

class SerializerInterfaceTest extends TestCase
{
    /**
     * @test
     */
    public function implementing_SerializerInterface_should_work_when_asJson_is_present()
    {
        ImplementedSerializer::asJson([]);

        $this->assertTrue(true);
    }
}

class ImplementedSerializer implements SerializerInterface
{
    public static function asJson($obj)
    {
        return ['a', 'b', 'c'];
    }
}
