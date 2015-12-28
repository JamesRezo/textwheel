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
class CallbackSplitReplacement extends Replacement implements ReplacementInterface
{
    /**
     * glue for implode ending split rule.
     *
     * @var null|string
     */
    protected $glue = null;

    /**
     * {@inheritdoc}
     *
     * @param array  $args Properties of the rule
     */
    public function __construct($name, array $args)
    {
        parent::__construct($name, $args);

        $this->match = array(
            $this->match,
            is_null($this->glue) ? $this->match : $this->glue
        );
    }

    /**
     * Callback split replacement.
     *
     * @param  String $text The input text
     *
     * @return string       The output text
     */
    protected function replace($text)
    {
        $splitText = explode($this->match[0], $text);
        $text = join($this->match[1], array_map($this->replace, $splitText));

        return $text;
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    protected function initialize()
    {
        if (is_array($this->match)) {
            throw new \InvalidArgumentException('match argument for split rule can\'t be an array');
        }
        if (isset($this->glue) and is_array($this->glue)) {
            throw new \InvalidArgumentException('glue argument for split rule can\'t be an array');
        }
    }
}
