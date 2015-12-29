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
use TextWheel\Test\fixtures\RuleTest;

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

    protected function requireFile($file)
    {
        require_once $this->fixtures . $file;
    }

    protected function getConditions(array $args = array())
    {
        if (empty($args)) {
            $args = array(
                'if_match' => '/by myself/',
            );
        }

        return Factory::createConditions($args);
    }

    protected function minimalArguments()
    {
        return array(
            'match' => '/test/',
            'replace' => 'text',
        );
    }

    protected function getReplacement(array $args = array(), $name = 'a PregRule')
    {
        if (empty($args)) {
            $args = $this->minimalArguments();
        }

        return Factory::createReplacement($args, $name);
    }

    protected function getRule(array $args = array())
    {
        if (empty($args)) {
            $args = $this->minimalArguments();
        }

        return new RuleTest('RuleTest', $args);
    }
}
