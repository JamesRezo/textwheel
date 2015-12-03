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
 * Replacement of all text
 */
class AllRule extends Rule implements RuleInterface
{
    /**
     * {@inheritdoc}
     *
     * @param string $name The name of the rule
     * @param array  $args Properties of the rule
     */
    public function __construct($name, array $args)
    {
        $this->type = 'all';
        unset($args['type']);
        $args['match'] = '';

        parent::__construct($name, $args);
    }

    /**
     * {@inheritdoc}
     *
     * @param  string $text The input text
     *
     * @return string       The output text
     */
    public function replace($text)
    {
        # special case: replace $0 with $t
        #   replace: "A$0B" will surround the string with A..B
        #   replace: "$0$0" will repeat the string
        if (strpos($this->replace, '$0') !== false) {
            $text = str_replace('$0', $text, $this->replace);
        } else {
            $text = $this->replace;
        }

        return $text;
    }

    /**
     * Callback replacement of all text.
     *
     * @param  String $text The input text
     *
     * @return string       The output text
     */
    public function callback($text)
    {
        return $this->replace($text);
    }
}
