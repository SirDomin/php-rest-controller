<?php

namespace App\Converter;

class Converter
{
    public static function convertTableToObject(string $table): string
    {
        return sprintf('App\Entity\%s', ucfirst($table));
    }
}