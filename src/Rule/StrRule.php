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

class StrRule extends Rule implements RuleInterface
{
    public function __construct($name, array $args)
    {
        $this->type = 'str';
        unset($args['type']);

        parent::__construct($name, $args);
    }

    public function replace($text)
    {
        if (!is_string($this->match) or strpos($text, $this->match) !== false) {
            $text = str_replace($this->match, $this->replace, $text);
        }

        return $text;
    }
}
