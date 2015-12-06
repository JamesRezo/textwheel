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

namespace TextWheel\Test;

use PHPUnit_Framework_TestCase;
use TextWheel\Factory;

/**
 * Main tests case.
 *
 * @author James Hautot <james@rezo.net>
 */
class TestCase extends PHPUnit_Framework_TestCase
{
    protected $text = 'This is a simple test written by myself';

    protected $fixtures;

    protected function setUp()
    {
        $this->fixtures = __DIR__ . '/fixtures/';
    }

    protected function getCondition(array $args = array())
    {
        if (empty($args)) {
            $args = array(
                'if_match' => '/by myself/',
            );
        }

        return Factory::createCondition($args);
    }

    protected function getReplacement(array $args = array())
    {
        if (empty($args)) {
            $args = array(
                'match' => '/test/',
                'replace' => 'text',
            );
        }

        return Factory::createReplacement($args);
    }

    protected function getRule($name = 'a PregRule', array $args = array())
    {
        if (empty($args)) {
            $args = array(
                'match' => '/test/',
                'replace' => 'text',
            );
        }
        $class = 'TextWheel\Rule\\'.$type.'Rule';

        return new $class($name, $args);
    }
}
