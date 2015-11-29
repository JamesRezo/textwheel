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
 * Replacement via str_replace or strtr_replace functions.
 */
class StrRule extends Rule implements RuleInterface
{
    protected $strtr = false;

    /**
     * {@inheritdoc}
     *
     * @param string $name The name of the rule
     * @param array  $args Properties of the rule
     */
    public function __construct($name, array $args)
    {
        $this->type = 'str';
        unset($args['type']);

        parent::__construct($name, $args);
    }

    protected function initialize()
    {
        parent::initialize($args);

        // test if quicker strtr usable
        if (is_array($this->match) and is_array($this->replace)
            and $c = array_map('strlen', $this->match)
            and $c = array_unique($c)
            and count($c) == 1
            and reset($c) == 1
            and $c = array_map('strlen', $this->replace)
            and $c = array_unique($c)
            and count($c) == 1
            and reset($c) == 1
        ) {
            $this->match = implode('', $this->match);
            $this->replace = implode('', $this->replace);
            $this->strtr = true;
        }
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
        if ($this->strtr) {
            return $this->replaceStrtr($text);
        }

        if (!is_string($this->match) or strpos($text, $this->match) !== false) {
            $text = str_replace($this->match, $this->replace, $text);
        }

        return $text;
    }

    /**
     * Fast Static string replacement one char to one char.
     *
     * @param  string $text The input text
     *
     * @return string       The output text
     */
    public function replaceStrtr($text)
    {
        return strtr($text, $this->match, $this->replace);
    }
}
