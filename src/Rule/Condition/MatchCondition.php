<?php

namespace TextWheel\Rule\Condition;

class MatchCondition
{
    protected $condition = '';

    public function __construct($condition)
    {
        $this->condition = $condition;
    }

    public function appliesTo($text)
    {
        return preg_match($this->condition, $text);
    }
}
