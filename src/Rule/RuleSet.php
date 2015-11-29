<?php

namespace TextWheel\Rule;

class RuleSet extends BaseRule implements RuleInterface
{
    private $rules;

    private $sorted = true;

    public function __construct($name, array $args)
    {
        parent::__construct($name, $args);

        $this->rules = array();
    }

    protected function initialize($args)
    {
    }

    public function isWheel()
    {
        return true;
    }

    public function add(RuleInterface $rule)
    {
        $this->rules[$rule->getName()] = $rule;
        $this->sorted = false;

        return $this;
    }

    public function remove(RuleInterface $rule)
    {
        unset($this->rules[$rule->getName()]);
        $this->sorted = false;

        return $this;
    }

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
     * Sort rules according to priority and purge disabled rules
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
