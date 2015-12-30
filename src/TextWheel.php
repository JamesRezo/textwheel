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

namespace TextWheel;

use TextWheel\Factory;
use TextWheel\Utils\File;

/**
 * The Main object of the libray.
 */
class TextWheel
{
    /** @var Wheel The Rules */
    protected $ruleset;

    /**
     * Base TextWheel Contructor.
     *
     * @param string|array $ruleset a file or an array of rules
     */
    public function __construct($ruleset)
    {
        if (is_string($ruleset)) {
            $ruleset = $this->loadFile($ruleset);
        }

        if (!is_array($ruleset) or empty($ruleset)) {
            throw new \InvalidArgumentException("Error Processing Request", 1);
        }

        foreach ($ruleset as $name => $rules) {
            $this->ruleset = Factory::createReplacement($rules, $name);
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
        return $this->ruleset->apply($text);
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
