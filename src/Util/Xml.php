<?php

namespace ApiMetal\Util;

use \Medio\Inflector;

class Xml
{
    /**
     * Given a Json string, produce and xml object
     * that corresponds to the JSON entity.
     *
     * @param  string   $jsonString  The Json string to convert to xml
     * @param  string   $root        The xml root node name
     * @param  boolean  $xmlRoot     Should the returned object contain an <xml/> node above the root?
     * @return                       An xml object describing the json input
     */
    public static function fromJsonString($jsonString, $root = 'response', $xmlRoot = false)
    {
        $root = self::getRoot($root);
        $jsonObj = json_decode($jsonString);

        return self::fromJsonObj($jsonObj, $root);
    }

    /**
     * Given a Json object, ie, a Json string that
     * has been decoded to a native PHP object, produce
     * an xml object that corresponds to the JSON entity
     *
     * @param  string   $jsonObj     The object produced by json_decode-ing some JSON string
     * @param  string   $root        The xml root node name
     * @param  boolean  $xmlRoot     Should the returned object contain an <xml/> node above the root?
     * @return                       An xml object describing the json input
     */
    public static function fromJsonObj($jsonObj, $root = 'response', $xmlRoot = false)
    {
        $root = self::getRoot($root);

        $xml = new \SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\"?><$root/>");
        self::arrayToXml($jsonObj, $xml);
        $xml = $xml->asXML();

        if (!$xmlRoot) {
            $dom = new \DOMDocument();
            $dom->loadXML($xml);
            $xml = $dom->saveXML($dom->documentElement);
        }

        return $xml;
    }

    /**
     * Given an array, or an object that can be cast as an array,
     * and a SimpleXML object, convert that array to XML and add
     * it to the SimpleXML object.
     *
     * @param  array        $array          The array to convert to XML and add to
     *                                      the SimpleXML object
     * @param  \SimpleXML   $xml            The SimpleXML object that should receive
     *                                      the XML produced by the array
     * @param  string       $parentNodeName The parent node name (ie, array key of the
     *                                      current $array's parent), if one exists.
     */
    public static function arrayToXml($array, &$xml, $parentNodeName = null)
    {
        $array = (array) $array;

        foreach ($array as $key => $value) {
            if (is_object($value)) {
                $value = (array) $value;
            }
            if (is_array($value)) {
                if (!is_numeric($key)) {
                    $subnode = $xml->addChild("$key");
                    self::arrayToXml($value, $subnode, $key);
                } else {
                    $subnode = $xml->addChild(Inflector::singularize($parentNodeName));
                    self::arrayToXml($value, $subnode, $parentNodeName);
                }
            } else {
                if (!is_numeric($key)) {
                    $xml->addChild("$key", htmlspecialchars("$value"));
                } else {
                    $key = ($parentNodeName == null ? $key : Inflector::singularize($parentNodeName));
                    $xml->addChild("$key", htmlspecialchars("$value"));
                }
            }
        }
    }

    /**
     * Get a root element for the xml structure;
     * either the string supplied to the parent
     * function, or the default "response"
     *
     * @param  string $root The root string supplied to the calling function
     * @return sting        The original root string, if supplied, or "response"
     */
    private static function getRoot($root)
    {
        if ($root == null) {
            $root = 'response';
        }

        return $root;
    }
}
