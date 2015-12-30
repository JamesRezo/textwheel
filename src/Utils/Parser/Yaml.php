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

namespace TextWheel\Utils\Parser;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;

/**
 * Yaml parser.
 */
class Yaml implements ParserInterface
{
    /**
     * Parsing following the YAML format.
     *
     * @param  string $content YAML formatted rules
     *
     * @return array           rules as array
     */
    public static function parse($content = '')
    {
        $yaml = new Parser();

        try {
            $rules = $yaml->parse($content);
        } catch (ParseException $e) {
            return null;
        }

        return $rules;
    }
}
