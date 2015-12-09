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
 * Base Replacement Object.
 */
abstract class Replacement implements ReplacementInterface
{
    /** @var array|string replacements */
    protected $replace;

    /** @var array|string patterns to replace */
    protected $match;

    /**
     * Base constructor.
     *
     * @param array $args
     */
    public function __construct(array $args)
    {
        foreach ($args as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
        $this->initialize();
    }

    /**
     * Sets custom properties for the rule.
     *
     * @return void
     */
    protected function initialize()
    {
    }

    /**
     * {@inheritdoc}
     *
     * @param  string $text The input text
     *
     * @throws Exception    In case the replacement cannot compute
     *
     * @return string       The output text
     */
    abstract public function replace($text);
}
