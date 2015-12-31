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

namespace TextWheel\Utils;

use TextWheel\TextWheel;
use TextWheel\Condition\ConditionInterface;
use TextWheel\Replacement\ReplacementInterface;

/**
 * Rule Compiler
 */
class Compiler
{
    /**
     * [compileCondition description].
     *
     * @param  ConditionInterface $condition [description]
     * @param  string             $code      [description]
     *
     * @return string                        The compiled code
     */
    protected function compileCondition(ConditionInterface $condition, $code = '')
    {
        return 'if (' . $condition->getCompiledCode() . ') { ' . $code . ' }';
    }

    /**
     * [compileReplacement description].
     *
     * @param  ReplacementInterface $replacement [description]
     *
     * @return string                            The compiled code
     */
    protected function compileReplacement(ReplacementInterface $replacement)
    {
        $code = $replacement->getCompiledCode();

        foreach ($replacement->getConditions() as $condition) {
            $code = $this->compileCondition($condition, $code);
        }

        return $code;
    }

    /**
     * Compile the replacement and conditions in code.
     *
     * @return string The compiled code
     */
    public function compile(ReplacementInterface $replacement)
    {
        $code = '';

        if (!$replacement->isDisabled()) {
            $code = $this->compileReplacement($replacement) . ' ';
        }

        return $code . 'return $text;';
    }
}
