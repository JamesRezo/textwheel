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
 * Callback Preg Replacement Object.
 */
class CallbackPregReplacement extends Replacement implements ReplacementInterface
{
    /**
     * {@inheritdoc}
     *
     * @param  string $text     The input text
     *
     * @throws RuntimeException In case the replacement cannot compute
     *
     * @return string           The output text
     */
    public function replace($text)
    {
        $text = preg_replace_callback($this->match, $this->replace, $text, -1);

        if (is_null($text)) {
            throw new \RuntimeException('Memory error, increase pcre.backtrack_limit in php.ini');
        }

        return $text;
    }
}