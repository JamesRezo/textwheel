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
     * Encapsulates code with a condition.
     *
     * @param  ConditionInterface $condition The Condition to compile
     * @param  string             $code      The code to encapsulate with the condition
     *
     * @return string                        The compiled code
     */
    protected function compileCondition(ConditionInterface $condition, $code = '')
    {
        return 'if (' . $condition->getCompiledCode() . ') { ' . $code . ' }';
    }

    /**
     * Compiles a Rule.
     *
     * @param  ReplacementInterface $replacement The Rule to compile
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
     * Compiles the replacement and conditions of a Rule in code.
     *
     * @param  ReplacementInterface $replacement The Rule to compile
     *
     * @return string                            The compiled code
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
