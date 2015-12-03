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
 * Replacement using PCRE preg_* functions.
 */
class PregRule extends Rule implements RuleInterface
{
    /**
     * {@inheritdoc}
     *
     * @param string $name The name of the rule
     * @param array  $args Properties of the rule
     */
    public function __construct($name, array $args)
    {
        $this->type = 'preg';
        unset($args['type']);

        parent::__construct($name, $args);
    }

    /**
     * {@inheritdoc}
     *
     * @param  string $text The input text
     *
     * @throws Exception    if pcre parameter is insufficient
     *
     * @return string       The output text
     */
    public function replace($text)
    {
        $text = preg_replace($this->match, $this->replace, $text, -1);

        if (is_null($text)) {
            throw new \Exception('Memory error, increase pcre.backtrack_limit in php.ini');
        }

        return $text;
    }

    /**
     * Callback preg replacement.
     *
     * @param  String $text The input text
     *
     * @return string       The output text
     */
    public function callback($text)
    {
        $text = preg_replace_callback($this->match, $this->replace, $text, -1);

        if (is_null($text)) {
            throw new \Exception('Memory error, increase pcre.backtrack_limit in php.ini');
        }

        return $text;
    }
}
