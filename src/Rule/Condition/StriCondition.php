<?php

namespace TextWheel\Rule\Condition;

class StriCondition
{
    protected $condition = '';

    public function __construct($condition)
    {
        $this->condition = $condition;
    }

    public function appliesTo($text)
    {
        return stripos($text, $this->condition) !== false;
    }
}
