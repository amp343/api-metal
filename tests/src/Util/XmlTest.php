<?php

namespace ApiMetal\Tests\Util;

use ApiMetal\Tests\TestCase;
use ApiMetal\Util\Xml;

class XmlTest extends TestCase
{
    protected function setUp()
    {
        //
        // create an array that can be encoded to
        // a json string, along with an object
        // representing that array cast as an object.
        //
        // both should produce valid output from the
        // Xml class' methods.
        //
        // save both as json_encoded strings
        //
        $arr = [
            'favorite_day' => 'saturday',
            'messages' => [
                'this is message 1',
                'this is message 2',
                'this is message 3'
            ],
            'nested' => [
                'nests' => [
                    ['a', 'b', 'c'],
                    ['x', 'y', 'z'],
                ]

            ],
            'meta_statements' => [
                'this is getting really meta',
                'info' => 'some info'
            ]
        ];
        $obj = (object) $arr;

        $this->expectedXmlBase = '
                <favorite_day>saturday</favorite_day>
                <messages>
                    <message>this is message 1</message>
                    <message>this is message 2</message>
                    <message>this is message 3</message>
                </messages>
                <nested>
                    <nests>
                        <nest>
                            <nest>a</nest>
                            <nest>b</nest>
                            <nest>c</nest>
                        </nest>
                        <nest>
                            <nest>x</nest>
                            <nest>y</nest>
                            <nest>z</nest>
                        </nest>
                    </nests>
                </nested>
                <meta_statements>
                    <meta_statement>this is getting really meta</meta_statement>
                    <info>some info</info>
                </meta_statements>
        ';
        $this->expectedXmlWithCustomRoot = '<some_root>' . $this->expectedXmlBase . '</some_root>';
        $this->expectedXmlWithDefaultRoot = '<response>' . $this->expectedXmlBase . '</response>';
        $this->expectedXmlWithXmlRoot = '<?xml version="1.0"?>' . $this->expectedXmlWithDefaultRoot;

        $this->jsonStringFromArr = json_encode($arr);
        $this->jsonStringFromObj = json_encode($obj);
    }

    /**
     * @test
     */
    public function testFromJsonStringUsingArray()
    {
        $xml = Xml::fromJsonString($this->jsonStringFromArr, 'some_root');
        $this->assertXmlStringEqualsXmlString($xml, $this->expectedXmlWithCustomRoot);
    }

    /**
     * @test
     */
    public function testFromJsonStringUsingObject()
    {
        $xml = Xml::fromJsonString($this->jsonStringFromObj, 'some_root');
        $this->assertXmlStringEqualsXmlString($xml, $this->expectedXmlWithCustomRoot);
    }

    /**
     * @test
     */
    public function testFromJsonObjectUsingArray()
    {
        $xml = Xml::fromJsonObj(json_decode($this->jsonStringFromArr), 'some_root');
        $this->assertXmlStringEqualsXmlString($xml, $this->expectedXmlWithCustomRoot);
    }

    /**
     * @test
     */
    public function testFromJsonObjectUsingObject()
    {
        $xml = Xml::fromJsonObj(json_decode($this->jsonStringFromObj), 'some_root');
        $this->assertXmlStringEqualsXmlString($xml, $this->expectedXmlWithCustomRoot);
    }

    /**
     * @test
     */
    public function testWithDefaultRoot()
    {
        $xml = [];
        $xml[] = Xml::fromJsonString($this->jsonStringFromArr);
        $xml[] = Xml::fromJsonString($this->jsonStringFromObj);
        $xml[] = Xml::fromJsonObj(json_decode($this->jsonStringFromObj));
        $xml[] = Xml::fromJsonObj(json_decode($this->jsonStringFromArr));

        foreach ($xml as $x) {
            $this->assertXmlStringEqualsXmlString($x, $this->expectedXmlWithDefaultRoot);
        }
    }

    /**
     * @test
     */
    public function testWithXmlBase()
    {
        $xml = [];
        $xml[] = Xml::fromJsonString($this->jsonStringFromArr, null, true);
        $xml[] = Xml::fromJsonString($this->jsonStringFromObj, null, true);
        $xml[] = Xml::fromJsonObj(json_decode($this->jsonStringFromObj), null, true);
        $xml[] = Xml::fromJsonObj(json_decode($this->jsonStringFromArr), null, true);

        foreach ($xml as $x) {
            $this->assertXmlStringEqualsXmlString($x, $this->expectedXmlWithXmlRoot);
        }
    }
}
