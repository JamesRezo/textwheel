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
 * Callback replacement of all text.
 */
class CallbackAllReplacement extends Replacement implements ReplacementInterface
{
    /**
     * {@inheritdoc}
     *
     * @param  string $text The input text
     *
     * @throws Exception    In case the replacement cannot compute
     *
     * @return string       The output text
     */
    public function replace($text)
    {
        $f = $this->replace;

        return call_user_func($f, $text);
    }
}
