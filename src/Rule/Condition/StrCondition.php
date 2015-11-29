<?php

/**
 * TextWheel 0.1
 *
 * let's reinvent the wheel one last time
 *
 * This library of code is meant to be a fast and universal replacement
 * for any and all text-processing systems written in PHP
 *
 * It is dual-licensed for any use under the GNU/GPL2 and MIT licenses,
 * as suits you best
 *
 * (c) 2009 Fil - fil@rezo.net
 * Documentation & http://zzz.rezo.net/-TextWheel-
 *
 * Usage: $wheel = new TextWheel(); echo $wheel->text($text);
 *
 */

namespace TextWheel\Rule\Condition;

/**
 * Checks if a subtext is in a text.
 */
class StrCondition
{
    /** @var string the neede to chek in a haystack */
    protected $condition = '';

    /**
     * Str constructor.
     *
     * @param string $condition a subtext
     */
    public function __construct($condition)
    {
        $this->condition = $condition;
    }

    /**
     * {@inheritdoc}
     *
     * @param  string  $text a text input
     *
     * @return boolean       true if the rule applies to the input text
     */
    public function appliesTo($text)
    {
        return strpos($text, $this->condition) !== false;
    }
}
