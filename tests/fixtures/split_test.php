<?php

function split_test($text)
{
    return $text.'split';
}

function preg_test($matches)
{
    return str_rot13($matches[1]);
}

class Foo
{
    public static function Bar($text)
    {
        return $text.'FooBar';
    }

    public static function Baz($array)
    {
        return '('.$array[1].')';
    }
}
