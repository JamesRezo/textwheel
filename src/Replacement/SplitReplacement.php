<?php

/**
 * TextWheel 0.1.
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
 */

namespace TextWheel\Replacement;

/**
 * Replacement by spliting/joining text/array.
 */
class SplitReplacement extends Replacement implements ReplacementInterface
{
    /**
     * split replacement : invalid.
     *
     * @param string $text The input text
     *
     * @throws RuntimeException Need a Callback
     */
    protected function replace($text)
    {
        throw new \RuntimeException('split rule always needs a callback function as replace');
    }
}
