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
 * Composite Rule Object.
 */
class RuleSet extends BaseRule implements RuleInterface
{
    /** @var Rule[] List of Single rules or RuleSet */
    private $rules;

    /** @var boolean true if the list is sorted by priority */
    private $sorted = true;

    /**
     * {@inheritdoc}
     *
     * @param string $name The name of the rule
     * @param array  $args Properties of the rule
     */
    public function __construct($name, array $args)
    {
        parent::__construct($name, $args);

        $this->rules = array();
    }

    /**
     * {@inheritdoc}
     *
     * @param  array $args Properties of the rule
     *
     * @return void
     */
    protected function initialize(array $args)
    {
    }

    /**
     * {@inheritdoc}
     *
     * @param RuleInterface $rule a Rule to add
     */
    public function add(RuleInterface $rule)
    {
        $this->rules[$rule->getName()] = $rule;
        $this->sorted = false;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @param  RuleInterface $rule a Rule to remove
     */
    public function remove(RuleInterface $rule)
    {
        unset($this->rules[$rule->getName()]);
        $this->sorted = false;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * It applies all the replace() method of the rules that belongs to the Ruleset.
     *
     * @param  string $text The input text
     *
     * @return string       The output text
     */
    public function replace($text)
    {
        $this->sort();

        foreach ($this->rules as $name => $rule) {
            if (!$rule->hasCondition() || $rule->getCondition()->appliesTo($text)) {
                $text = $rule->replace($text);
            }
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
            foreach ($this->rules as $name => $rule) {
                if (!$rule->isDisabled()) {
                    $sortedRules[$rule->getPriority()][$name] = $rule;
                }
            }
            ksort($sortedRules, SORT_NUMERIC);
            $this->rules = array();
            foreach ($sortedRules as $rules) {
                $this->rules += $rules;
            }

            $this->sorted = true;
        }

        return $this;
    }
}
