<?php

namespace App\Parser;

class XmlParser
{
    public static function parseXML($xmlObject, $out = []): array
    {
        foreach ((array) $xmlObject as $index => $node)
            $out[$index] = (is_object($node)) ? self::parseXML($node) : $node;

        return $out;
    }
}