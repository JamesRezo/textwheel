<?php

/**
 * This file is part of TextWheel package.
 *
 * (c) 2009 Fil <fil@rezo.net>
 * http://zzz.rezo.net/-TextWheel-
 *
 * Dual licensed under the MIT and GPL2 licenses.
 * For the full copyright and license information, please view the LICENSE-MIT
 * and LICENSE-GPL files that was distributed with this source code.
 */

namespace TextWheel;

use TextWheel\Factory;
use TextWheel\Utils\File;
use TextWheel\Utils\Compiler;

/**
 * The Main object of the libray.
 */
class TextWheel
{
    /** @var ReplacmentInterface[] The Rules */
    protected $ruleset = array();

    /** @var Callable[] Store for compiled code */
    protected $compiled = array();

    /**
     * Base TextWheel Contructor.
     *
     * @param string|array $ruleset a file or an array of rules
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function __construct($ruleset = array())
    {
        if (is_string($ruleset)) {
            $ruleset = File::getArray($ruleset);
        }

        if (!is_array($ruleset) or empty($ruleset)) {
            throw new \InvalidArgumentException("Error Processing Request", 1);
        }

        foreach ($ruleset as $name => $rules) {
            $this->ruleset[] = Factory::createReplacement($rules, $name);
        }
    }

    /**
     * Process all rules of RuleSet to a text.
     *
     * @param  string $text The input text
     *
     * @return string       The output text
     */
    public function process($text)
    {
        foreach ($this->ruleset as $rule) {
            $text = $rule->apply($text);
        }

        return $text;
    }

    /**
     * Process all rules using compiled anonymous functions.
     *
     * @param  string $text The input text
     *
     * @return string       The output text
     */
    public function text($text)
    {
        if (empty($this->compiled)) {
            $this->compile();
        }

        foreach (array_values($this->compiled) as $compiledRule) {
            $text = $compiledRule($text);
        }

        return $text;
    }

    /**
     * Compile all Rules as an array of anonymous functions.
     *
     * @return void
     */
    protected function compile()
    {
        $compiler = new Compiler();

        foreach ($this->ruleset as $rule) {
            $this->compiled[$rule->getName()] = $compiler->compile($rule);
        }
    }
}
