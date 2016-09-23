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

use TextWheel\Utils\File;
use TextWheel\Utils\Compiler;
use TextWheel\Replacement\Wheel;

/**
 * The Main object of the libray.
 */
class TextWheel
{
    /** @var Wheel The Rules */
    protected $wheel;

    /** @var Compiler Store for compiled code */
    protected $compiler;

    /**
     * Base TextWheel Contructor.
     *
     * @param string|array $ruleset a file or an array of rules
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function __construct($ruleset = array())
    {
        $this->wheel = new Wheel('__root__', array());
        $this->compiler = new Compiler();

        $this->addRules($ruleset);
    }

    public function addRules($ruleset = array())
    {
        if (is_string($ruleset)) {
            $ruleset = File::getArray($ruleset);
        }

        if (!is_array($ruleset)) {
            throw new \InvalidArgumentException('Error Processing Request', 1);
        }

        foreach ($ruleset as $name => $rule) {
            $this->wheel->add(Factory::createReplacement($rule, $name));
        }
    }

    /**
     * Process all rules of RuleSet to a text.
     *
     * @param string $text The input text
     *
     * @return string The output text
     */
    public function process($text)
    {
        return $this->wheel->apply($text);
    }

    /**
     * Process all rules using compiled anonymous functions.
     *
     * @param string $text The input text
     *
     * @return string The output text
     */
    public function text($text)
    {
        static $compiledWheel = null;

        if (is_null($compiledWheel)) {
            $compiledWheel = $this->compiler->compile($this->wheel);
        }

        return $compiledWheel($text);
    }
}
