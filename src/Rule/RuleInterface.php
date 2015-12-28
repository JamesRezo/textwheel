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

namespace TextWheel\Rule;

/**
 * Rule Interface.
 */
interface RuleInterface
{
    /**
     * Gets the name of the rule.
     *
     * @return string The name of the rule
     */
    public function getName();

    /**
     * Gets the priority of the rule.
     *
     * @return integer The priority of the rule
     *
     * @see RuleSet::sort()
     */
    public function getPriority();

    /**
     * Tells if the rule is disabled.
     *
     * @return boolean true if the rule is disabled
     */
    public function isDisabled();

    /**
     * Disable the rule.
     *
     * @return void
     */
    public function setDisabled();
}
