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

abstract class Rule extends BaseRule implements RuleInterface
{
    protected $type;

    protected $match;

    protected $replace;

    protected function initialize($args)
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

    public function isWheel()
    {
        return false;
    }

    public function add(RuleInterface $rule)
    {
        return false;
    }

    public function remove(RuleInterface $rule)
    {
        return false;
    }

    public function getType()
    {
        return $this->type;
    }
}
