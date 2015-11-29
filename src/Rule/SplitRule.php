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

class SplitRule extends Rule implements RuleInterface
{
    /**
     * glue for implode ending split rule.
     *
     * @var null|String
     */
    protected $glue = null;

    public function __construct(array $args)
    {
        $this->type = 'split';
        unset($args['type']);

        parent::__construct($args);
    }

    protected function checkValidity()
    {
        parent::checkValidity();

        if (is_array($this->match)) {
            throw new \InvalidArgumentException('match argument for split rule can\'t be an array');
        }
        if (isset($this->glue) and is_array($this->glue)) {
            throw new \InvalidArgumentException('glue argument for split rule can\'t be an array');
        }
    }
   
    /**
     * split replacement : invalid.
     *
     * @param  String $text The input text
     *
     * @throws Exception Need a Callback
     */
    public function replace($text)
    {
        throw new \Exception('split rule always needs a callback function as replace');
    }
}
