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

abstract class Rule
{
    protected $type;

    protected $match;

    protected $replace;

    /** @var boolean true if rule is disabled */
    protected $disabled = false;

    /** @var integer rule priority (rules are applied in ascending order) */
    protected $priority = 0;

    /**
     * Rule constructor.
     *
     * @param array $args Properties of the rule
     */
    public function __construct(array $args)
    {
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

        foreach ($args as $k => $v) {
            if (property_exists($this, $k)) {
                $this->$k = $args[$k];
            }
        }
        $this->checkValidity(); // check that the rule is valid
    }

    /**
     * Rule checker.
     *
     * @return void
     */
    protected function checkValidity()
    {
        #mandatory args
        foreach (array('type', 'match', 'replace') as $property) {
            if (!isset($this->$property)) {
                throw new \InvalidArgumentException($property.' must be defined');
            }
        }
    }

    public function getType()
    {
        return $this->type;
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
}
