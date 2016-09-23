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
 * Fallback Replacement Object.
 */
class IdentityReplacement extends Replacement implements ReplacementInterface
{
    /**
     * {@inheritdoc}
     *
     * @param string $text The input text
     *
     * @return string The output text
     */
    protected function replace($text)
    {
        return $text;
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize()
    {
        $this->setDisabled();
    }
}
