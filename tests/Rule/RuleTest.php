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

namespace TextWheel\Test\Rule;

use TextWheel\Test\TestCase;

/**
 * Rule tests.
 *
 * @author James Hautot <james@rezo.net>
 */
class RuleTest extends TestCase
{
    public function dataDisabled()
    {
        $data = array();

        $data['disabled by argument'] = array(
            true,
            array('disabled' => true),
        );

        $data['replace property missing'] = array(
            true,
            array('match' => 'text'),
        );

        $data['unknown type'] = array(
            true,
            array('type' => 'unknown'),
        );

        $data['Preg Rule'] = array(
            false,
            $this->minimalArguments(),
        );

        $data['Split with glue'] = array(
            false,
            array('type' => 'split', 'is_callback' => true, 'match' => 'text', 'replace' => 'test', 'glue' => 'glue'),
        );

        return $data;
    }

    /**
     * @dataProvider dataDisabled
     */
    public function testDisabled($expected, $args)
    {
        $rule = $this->getReplacement($args);
        $this->assertSame($expected, $rule->isDisabled());
    }

    public function testDisabledBySetter()
    {
        $rule = $this->getRule();
        $rule->setDisabled();

        $this->assertTrue($rule->isDisabled());
    }

    public function dataInvalidArgs()
    {
        $data = array();

        $data['Match is an array'] = array(
            'an Invalid Split Rule',
            array('type' => 'split', 'is_callback' => true, 'match' => array('test1', 'test2'), 'replace' => 'test'),
        );

        $data['Glue is an array'] = array(
            'another Invalid Split Rule',
            array('type' => 'split', 'is_callback' => true, 'match' => 'test', 'replace' => 'test', 'glue' => array('glue')),
        );

        return $data;
    }

    /**
     * @expectedException InvalidArgumentException
     * @dataProvider dataInvalidArgs
     */
    public function testInvalidArgs($name, $args)
    {
        $this->getReplacement($args, $name);
    }

    public function dataPriority()
    {
        $data = array();

        $data['Default Priority'] = array(
            0,
            $this->minimalArguments(),
        );

        $data['Fallback Priority'] = array(
            0,
            array_merge(array('priority' => 'test'), $this->minimalArguments()),
        );

        $data['Set Priority'] = array(
            50,
            array_merge(array('priority' => 50), $this->minimalArguments()),
        );

        return $data;
    }

    /**
     * @dataProvider dataPriority
     */
    public function testPriority($expected, $args)
    {
        $rule = $this->getRule($args);

        $this->assertSame($expected, $rule->getPriority());
    }

    public function dataName()
    {
        $data = array();

        $data['default'] = array(
            '',
            null,
        );

        $data['numeric'] = array(
            0,
            0,
        );

        $data['some string'] = array(
            'some string',
            'some string',
        );

        $data['non-scalar 1'] = array(
            '',
            array('name'),
        );

        $data['non-scalar 2'] = array(
            '',
            new \StdClass,
        );

        return $data;
    }

    /**
     * @dataProvider dataName
     */
    public function testName($expected, $name)
    {
        $rule = $this->getRule(array(), $name);

        $this->assertSame($expected, $rule->getName());
    }
}
