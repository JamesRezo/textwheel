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
 * Single Rule Object.
 */
abstract class Rule extends BaseRule implements RuleInterface
{
    /** @var string Type of the rule */
    protected $type;

    /** @var array|string patterns to replace */
    protected $match;

    /** @var array|string replacements */
    protected $replace;

    /**
     * {@inheritdoc}
     *
     * @param  array $args Properties of the rule
     *
     * @return void
     */
    protected function initialize(array $args)
    {
        foreach ($args as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Rule checker.
     *
     * @throws InvalidArgumentException if mandatory arguments are missing.
     *
     * @return void
     */
    protected function checkValidity()
    {
        parent::checkValidity();

        #mandatory args
        foreach (array('type', 'match', 'replace') as $property) {
            if (!isset($this->$property)) {
                throw new \InvalidArgumentException($property.' must be defined');
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param RuleInterface $rule a Rule to add
     */
    public function add(RuleInterface $rule)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @param  RuleInterface $rule a Rule to remove
     */
    public function remove(RuleInterface $rule)
    {
        return $this;
    }

    /**
     * Gets the type og the rule.
     *
     * @return string Type of the rule
     */
    public function getType()
    {
        return $this->type;
    }
}
