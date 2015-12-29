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

use TextWheel\Rule\AbstractRule;

/**
 * Base Replacement Object.
 */
abstract class Replacement extends AbstractRule implements ReplacementInterface
{
    /** @var array|string Replacements */
    protected $replace;

    /** @var array|string Patterns to replace */
    protected $match;

    /**
     * Base Replacement constructor.
     *
     * @param string $name The name of the rule
     * @param array  $args Properties of the rule
     */
    public function __construct($name, array $args)
    {
        parent::__construct($name, $args);

        if (isset($args['replace'])) {
            $this->replace = $args['replace'];
        }

        if (isset($args['match'])) {
            $this->match = $args['match'];
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
     * The effective replacement.
     *
     * @param  string $text The input text
     *
     * @throws Exception    In case the replacement cannot compute
     *
     * @return string       The output text
     */
    abstract protected function replace($text);

    /**
     * {@inheritdoc}
     *
     * @param ReplacementInterface $replacemet a Replacement to add
     */
    public function add(ReplacementInterface $replacement)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @param  string $text The input text
     *
     * @throws Exception    In case the replacement cannot work
     *
     * @return string       The output text
     */
    public function apply($text)
    {
        foreach ($this->conditions as $condition) {
            if (!$condition->appliesTo($text)) {
                return $text;
            }
        }

        return $this->replace($text);
    }
}
