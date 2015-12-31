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

namespace TextWheel\Condition;

/**
 * Condition Interface.
 */
interface ConditionInterface
{
    /**
     * A condition to verify if a rule applies to a text.
     *
     * @param  string  $text a text input
     *
     * @return boolean       true if the rule applies to the input text
     */
    public function appliesTo($text);

    /**
     * The compiled version of a condition test.
     *
     * @return string       the code encapsulated by the condition test
     */
    public function getCompiledCode();
}
