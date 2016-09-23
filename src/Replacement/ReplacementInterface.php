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
 * Replacement Interface.
 */
interface ReplacementInterface
{
    /**
     * Adds a replacement to Composite Replacement object.
     *
     * @param ReplacementInterface $replacemet a Replacement to add
     */
    public function add(ReplacementInterface $replacement);

    /**
     * The conditional application of replacement.
     *
     * @param string $text The input text
     *
     * @throws RuntimeException In case the replacement cannot compute
     *
     * @return string The output text
     */
    public function apply($text);
}
