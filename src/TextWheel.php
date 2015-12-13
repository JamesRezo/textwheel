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
use TextWheel\Rule\RuleSet;

/**
 * The Main object of the libray.
 */
class TextWheel
{
    /** @var RuleSet The Rules */
    protected $ruleset;

    /**
     * Constructor.
     *
     * @param RuleSet $ruleset
     */
    public function __construct()
    {
        $this->setRuleSet($ruleset);
    }

    /**
     * Set RuleSet
     * @param TextWheelRuleSet $ruleset
     */
    public function setRuleSet(RuleSet $ruleset = null)
    {
        if (!($ruleset instanceof RuleSet)) {
            $ruleset = Factory::buildRuleSet($ruleset);
        }
        $this->ruleset = $ruleset;

        return $this;
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
