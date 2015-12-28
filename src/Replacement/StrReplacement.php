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

namespace TextWheel\Replacement;

/**
 * Replacement by spliting/joining text/array.
 */
class StrReplacement extends Replacement implements ReplacementInterface
{
    /** @var boolean use strtr as replacement function if true */
    protected $strtr = false;

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    protected function initialize()
    {
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
     * split replacement : invalid.
     *
     * @param  String $text The input text
     *
     * @throws Exception Need a Callback
     */
    protected function replace($text)
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
    protected function replaceStrtr($text)
    {
        return strtr($text, $this->match, $this->replace);
    }
}
