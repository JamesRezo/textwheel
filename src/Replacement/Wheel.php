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

namespace TextWheel\Replacement;

use TextWheel\Rule\AbstractRule;

/**
 * Composite Replacement Object.
 */
class Wheel extends AbstractRule implements ReplacementInterface
{
    /** @var Replacement[] List of replacements or wheels */
    private $replacements;

    /** @var boolean true if the list is sorted by priority */
    private $sorted = true;

    /**
     * Wheel constructor.
     *
     * @param string $name The name of the rule
     * @param array  $args Properties of the rule
     */
    public function __construct($name, array $args)
    {
        parent::__construct($name, $args);

        $this->replacements = array();
    }

    /**
     * {@inheritdoc}
     *
     * @param ReplacementInterface $replacemet a Replacement to add
     */
    public function add(ReplacementInterface $replacement)
    {
        $this->replacements[$replacement->getName()] = $replacement;
        $this->sorted = false;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @param  string $text The input text
     *
     * @throws Exception    In case the replacement cannot compute
     *
     * @return string       The output text
     */
    public function apply($text)
    {
        $this->sort();

        foreach ($this->replacements as $replacement) {
            $text = $replacement->apply($text);
        }

        return $text;
    }

    /**
     * Sort rules according to priority and purge disabled rules.
     */
    protected function sort()
    {
        if (!$this->sorted) {
            $sortedRules = array();
            foreach ($this->replacements as $name => $replacement) {
                if (!$replacement->isDisabled()) {
                    $sortedRules[$replacement->getPriority()][$name] = $replacement;
                }
            }
            ksort($sortedRules, SORT_NUMERIC);
            $this->replacements = array();
            foreach ($sortedRules as $replacements) {
                $this->replacements += $replacements;
            }

            $this->sorted = true;
        }

        return $this;
    }
}
