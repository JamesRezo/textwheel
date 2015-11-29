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
 * Base Rule Object.
 */
abstract class BaseRule
{
    /** @var string                  The name of the rule */
    private $name;

    /** @var boolean                 true if the rule is disabled */
    protected $disabled = false;

    /** @var integer                 Rule priority (rules are applied in ascending order) */
    protected $priority = 0;

    /** @var ConditionInterface|null Condition to apply optionaly to the rule */
    protected $condition = null;

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
            unset($args['disabled']);
        }

        if (isset($args['priority'])) {
            if (is_int($args['priority'])) {
                $this->priority = $args['priority'];
            }
            unset($args['priority']);
        }

        $this->setCondition($args);
        $this->initialize($args);
        $this->checkValidity(); // check that the rule is valid
    }

    /**
     * Sets custom properties for the rule.
     *
     * @param  array $args Properties of the rule
     *
     * @return void
     */
    abstract protected function initialize(array $args);

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
        if (!is_string($this->name)) {
            throw new \InvalidArgumentException('The name of the rule must be a string.');
        }
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
    abstract public function replace($text);

    /**
     * Tells if the rule has condition applicable.
     *
     * @return boolean true if a condition is set
     */
    public function hasCondition()
    {
        return isset($this->condition);
    }

    /**
     * Gets the condition of the rule.
     *
     * @return ConditionInterface the condition to apply the rule
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * Sets an optional condition for this rule.
     *
     * @param array $args Properties of the rule
     *
     * @throws InvalidArgumentException if more than one condition defined
     */
    protected function setCondition(array $args)
    {
        static $conditions = array(
            'if_chars' => 'TextWheel\Rule\Condition\CharsCondition',
            'if_match' => 'TextWheel\Rule\Condition\MatchCondition',
            'if_str' => 'TextWheel\Rule\Condition\StrCondition',
            'if_stri' => 'TextWheel\Rule\Condition\StriCondition',
        );

        if ($condition = array_intersect_key($args, $conditions)) {
            if (count($condition) !== 1) {
                throw new \InvalidArgumentException('Too much conditions. Only one expected.');
            }

            $key =  key($condition);
            $value = $condition[$key];
            if ($key == 'if_str') {
                # optimization: strpos or stripos?
                if (strtolower($value) !== strtoupper($value)) {
                    $key = 'if_stri';
                }
            }
            $class = $conditions[$key];

            $this->condition = new $class($value);
        }
    }
}
