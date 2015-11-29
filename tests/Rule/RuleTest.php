<?php

/*
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

namespace TextWheel\Test\Rule;

use TextWheel\Test\TestCase;

class RuleTest extends TestCase
{
    public function dataValidArgs()
    {
        $data = array();

        $data['Preg Rule'] = array(
            'preg',
            'Preg',
            array('match' => 'text', 'replace' => 'test'),
        );

        $data['Split with glue'] = array(
            'split',
            'Split',
            array('match' => 'text', 'replace' => 'test', 'glue' => 'glue'),
        );

        return $data;
    }

    /**
     * @dataProvider dataValidArgs
     */
    public function testValidArgs($expected, $type, $args)
    {
        $rule = $this->getRule($type, $args);

        $this->assertFalse($rule->isDisabled());
        $this->assertTrue($expected === $rule->getType());
    }

    public function dataInvalidArgs()
    {
        $data = array();

        $data['Replace missing'] = array(
            'Preg',
            array('match' => 'test'),
        );

        $data['Match is an array'] = array(
            'Split',
            array('match' => array('test1', 'test2'), 'replace' => 'test'),
        );

        $data['Glue is an array'] = array(
            'Split',
            array('match' => 'test', 'replace' => 'test', 'glue' => array('glue')),
        );

        return $data;
    }

    /**
     * @expectedException InvalidArgumentException
     * @dataProvider dataInvalidArgs
     */
    public function testInvalidArgs($type, $args)
    {
        $rule = $this->getRule($type, $args);
    }
}
