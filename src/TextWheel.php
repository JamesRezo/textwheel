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

/**
 * The Main object of the libray.
 */
class TextWheel
{
    /** @var ReplacmentInterface[] The Rules */
    protected $ruleset = array();

    /**
     * Base TextWheel Contructor.
     *
     * @param string|array $ruleset a file or an array of rules
     */
    public function __construct($ruleset = array())
    {
        if (is_string($ruleset)) {
            $ruleset = $this->loadFile($ruleset);
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
            $text = $this->ruleset->apply($text);
        }

        return $text;
    }

    /**
     * Load a file describing rules.
     *
     * @param string $file
     *
     * @return array
     */
    private function loadFile($file)
    {
        try {
            $rules = File::getArray($file);
        } catch (Exception $e) {
            printf("Unable to parse the content of file '%s'", $file);
        }

        return $rules;
    }
}
