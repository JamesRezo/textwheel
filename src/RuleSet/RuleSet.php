<?php

/*
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

namespace TextWheel\RuleSet;

use TextWheel\Rule;

class RuleSet extends DataSet
{
    /** @var Rule[] List of rules */
    protected $rules = array();

    /** @var boolean sort flag */
    protected $sorted = true;

    /**
     * Constructor
     *
     * @param array|string $ruleset
     * @param string $filepath
     */
    public function __construct($ruleset = array(), $filepath = '')
    {
        if ($ruleset) {
            $this->addRules($ruleset, $filepath);
        }
    }

    /**
     * public static loader
     * can be overloaded to use memoization
     *
     * @param array $ruleset
     * @param string $callback
     * @param string $class
     * @return class
     */
    public static function loader($ruleset, $callback = '', $class = 'TextWheelRuleSet')
    {
        $ruleset = new $class($ruleset);
        if ($callback) {
            $callback($ruleset);
        }

        return $ruleset;
    }
    /**
     * Get an existing named rule in order to override it
     *
     * @param string $name
     * @return string
     */
    public function getRule($name)
    {
        return isset($this->rules[$name]) ? $this->rules[$name] : null;
    }
    
    /**
     * get sorted Rules
     * @return array
     */
    public function getRules()
    {
        $this->sort();

        return $this->rules;
    }

    /**
     * Add a rule.
     *
     * @param TextWheelRule $rule
     */
    public function addRule(RuleInterface $rule)
    {
        $this->rules[] = $rule;
        $this->sorted = false;

        return $this;
    }

    /**
     * Add a list of rules.
     *
     * can be
     * - an array of rules
     * - a string filename
     * - an array of string filename
     *
     * @param array|string $rules
     * @param string $filepath
     */
    public function addRules($rules, $filepath = '')
    {
        // rules can be an array of filename
        if (is_array($rules) and is_string(reset($rules))) {
            foreach ($rules as $i => $filename) {
                $this->addRules($filename);
            }
            return;
        }

        // rules can be a string : yaml filename
        if (is_string($rules)) {
            $file = $rules; // keep the real filename
            $rules = $this->loadFile($file, $filepath);
            $filepath = dirname($file).'/';
        }

        // rules can be an array of rules
        if (is_array($rules) and count($rules)) {
            # cast array-rules to objects
            foreach ($rules as $i => $rule) {
                if (is_array($rule)) {
                    $rules[$i] = new TextWheelRule($rule);
                }
                // load subwheels when necessary
                if ($rules[$i]->is_wheel) {
                    // subruleset is of the same class as current ruleset
                    $class = get_class($this);
                    $rules[$i]->replace = new $class($rules[$i]->replace, $filepath);
                }
            }
            $this->rules = array_merge($this->rules, $rules);
            $this->sorted = false;
        }

        return $this;
    }

    /**
     * Sort rules according to priority and
     * purge disabled rules
     *
     */
    protected function sort()
    {
        if (!$this->sorted) {
            $rulz = array();
            foreach ($this->rules as $index => $rule) {
                if (!$rule->isDisabled()) {
                    $rulz[$rule->getPriority()][$index] = $rule;
                }
            }
            ksort($rulz);
            $this->rules = array();
            foreach ($rulz as $rules) {
                $this->rules += $rules;
            }

            $this->sorted = true;
        }

        return $this;
    }
}
