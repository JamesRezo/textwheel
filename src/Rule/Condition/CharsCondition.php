<?php

namespace TextWheel\Rule\Condition;

class CharsCondition
{
    protected $condition = '';

    public function __construct($condition)
    {
        $this->condition = $condition;
    }

    public function appliesTo($text)
    {
        return strpbrk($t, $rule->if_chars) !== false;
    }
}
