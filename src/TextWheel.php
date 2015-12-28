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
use TextWheel\Replacement\Wheel;

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
        if (is_file($ruleset)) {
            $ruleset = Factory::loadFile($ruleset);
        }

        if (!is_array($ruleset)) {
            throw new \InvalidArgumentException("Error Processing Request", 1);
        }

        $name = isset($ruleset['name']) ? $ruleset['name'] : '';

        $this->ruleset = new Wheel($name, $ruleset);
    }

    /**
     * Process all rules of RuleSet to a text
     *
     * @param string $text
     *
     * @return string
     */
    public function process($text)
    {
        return $this->ruleset->apply($text);
    }
}
