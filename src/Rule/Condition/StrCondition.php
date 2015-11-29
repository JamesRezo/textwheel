<?php

namespace TextWheel\Rule\Condition;

class StrCondition
{
    protected $condition = '';

    public function __construct($condition)
    {
        $this->condition = $condition;
    }

    public function appliesTo($text)
    {
        return strpos($text, $this->condition) !== false;
    }
}
