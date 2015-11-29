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

namespace TextWheel\Rule;

abstract class BaseRule
{
    /** @var string The name of the rule */
    private $name;

    /** @var boolean true if rule is disabled */
    protected $disabled = false;

    /** @var integer rule priority (rules are applied in ascending order) */
    protected $priority = 0;

    /** @var Condition|null condition to apply the rule */
    protected $condition = null;

    /**
     * Rule constructor.
     *
     * @param array $args Properties of the rule
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

    abstract protected function initialize($args);

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

    public function getName()
    {
        return $this->name;
    }

    public function isDisabled()
    {
        return $this->disabled === true;
    }

    public function setDisabled()
    {
        $this->disabled = true;

        return $this;
    }

    public function getPriority()
    {
        return $this->priority;
    }

    abstract public function replace($text);

    public function hasCondition()
    {
        return isset($this->condition);
    }

    public function getCondition()
    {
        return $this->condition;
    }

    protected function setCondition($args)
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
            $class = $conditions[$key];

            $this->condition = new $class($value);
        }
    }
}
