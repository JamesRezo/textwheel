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

use TextWheel\Condition\ConditionInterface;
use TextWheel\Replacement\ReplacementInterface;
use TextWheel\Replacement\IdentityReplacement;
use TextWheel\Factory;

/**
 * Base Rule Object.
 */
class Rule implements RuleInterface
{
    /** @var string                     The name of the rule */
    private $name;

    /** @var boolean                    true if the rule is disabled */
    protected $disabled = false;

    /** @var integer                    Rule priority (rules are applied in ascending order) */
    protected $priority = 0;

    /** @var ConditionInterface[]       Conditions to apply optionaly to the rule */
    protected $conditions = array();

    /** @var ReplacementnInterface|null Replacement to apply to the rule */
    protected $replacement = null;

    /**
     * Base Rule constructor.
     *
     * @param string $name The name of the rule
     * @param array  $args Properties of the rule
     */
    public function __construct($name, array $args)
    {
        $this->name = $name;

        if (isset($args['disabled'])) {
            $this->disabled = (bool) $args['disabled'];
        }

        if (isset($args['priority'])
            and is_int($args['priority'])
        ) {
            $this->priority = $args['priority'];
        }

        $this->setReplacement($args);
        $this->setConditions($args);
        $this->checkValidity(); // check that the rule is valid
    }

    /**
     * Rule checker.
     *
     * @throws InvalidArgumentException if name property isn't a string.
     *
     * @return void
     */
    protected function checkValidity()
    {
        #name must be a string
        if (!is_scalar($this->name)) {
            throw new \InvalidArgumentException(
                'The name of the rule must be a scalar.'.
                var_export($this->name, true).
                '('.gettype($this->name).')'
            );
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

    /** {@inheritdoc} */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Tells if the rule is disabled.
     *
     * @return boolean true if the rule is disabled
     */
    public function isDisabled()
    {
        return $this->disabled === true;
    }

    /**
     * Disable the rule.
     */
    public function setDisabled()
    {
        $this->disabled = true;

        return $this;
    }

    /**
     * Gets the priority of the rule.
     *
     * @return integer priory of the rule
     *
     * @see RuleSet::sort()
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * {@inheritdoc}
     *
     * @param  string $text The input text
     *
     * @throws Exception    In case the replacement cannot work
     *
     * @return string       The output text
     */
    public function apply($text)
    {
        while (list(, $condition) = each($this->conditions)) {
            if (!$condition->appliesTo($text)) {
                return $text;
            }
        }

        return $this->replacement->replace($text);
    }

    /**
     * Sets the replacement strategy for this rule.
     *
     * @param array $args Properties of the rule
     */
    protected function setReplacement(array $args)
    {
        $this->replacement = Factory::createReplacement($args);
        if ($this->replacement instanceof IdentityReplacement) {
            $this->disabled = true;
        }
    }

    /**
     * Sets an optional condition for this rule.
     *
     * @param array $args Properties of the rule
     */
    protected function setConditions(array $args)
    {
        $this->conditions = Factory::createConditions($args);
    }
}
