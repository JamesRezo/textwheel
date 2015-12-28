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

use TextWheel\Factory;

/**
 * Abstract Rule Object.
 */
abstract class AbstractRule implements RuleInterface
{
    /** @var string  The name of the rule */
    private $name = '';

    /** @var integer Rule priority (rules are applied in ascending order) */
    protected $priority = 0;

    /** @var boolean true if the rule is disabled */
    protected $disabled = false;

    /** @var ConditionInterface[] Conditions to apply optionaly to the rule */
    protected $conditions = array();

    /**
     * Abstract Rule constructor.
     *
     * @param string $name The name of the rule
     * @param array  $args Properties of the rule
     */
    public function __construct($name, array $args)
    {
        if (is_scalar($name)) {
            $this->name = $name;
        }

        if (isset($args['priority'])
            and is_int($args['priority'])
        ) {
            $this->priority = $args['priority'];
        }

        if (isset($args['disabled'])) {
            $this->disabled = (bool) $args['disabled'];
        }

        $this->conditions = Factory::createConditions($args);
    }

    /**
     * {@inheritdoc}
     *
     * @return string The name of the rule
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     *
     * @return integer The priority of the rule
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
     * @return boolean true if the rule is disabled
     */
    public function isDisabled()
    {
        return $this->disabled === true;
    }

    /**
     * {@inheritdoc}
     */
    public function setDisabled()
    {
        $this->disabled = true;

        return $this;
    }
}
