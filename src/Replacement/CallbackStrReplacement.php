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
 * Callback string replacement.
 */
class CallbackStrReplacement extends Replacement implements ReplacementInterface
{
    /**
     * Callback string replacement.
     *
     * @param  String $text The input text
     *
     * @return string       The output text
     */
    protected function replace($text)
    {
        if (strpos($text, $this->match) !== false) {
            if (count($b = explode($this->match, $text)) > 1) {
                $f = $this->replace;
                $text = join($f($this->match), $b);
            }
        }

        return $text;
    }

    public function getCompiledCode()
    {
        return '$text = (strpos($text, ' . var_export($this->match, true) .') !== false) ?
            ((count($b = explode(' . var_export($this->match, true) .', $text)) > 1) ?
                $text = join(' . $this->replace .'(' . var_export($this->match, true) .'), $b)
            : $text)
        : $text;';
    }
}
