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

namespace TextWheel\Test\Replacement;

use TextWheel\Test\TestCase;
use TextWheel\Factory;

#use TextWheel\Replacement\IdendityReplacement;

/**
 * Replacement tests.
 *
 * @author James Hautot <james@rezo.net>
 */
class ReplacementTest extends TestCase
{
    public function dataReplace()
    {
        $data = array();

        $data['Fallback test'] = array(
            $this->text,
            array('replace' => '', 'type' => 'unknown')
        );

        $data['All with $0'] = array(
            '-- ' . $this->text . ' --',
            array('replace' => '-- $0 --', 'type' => 'all')
        );

        $data['All without $0'] = array(
            'replace',
            array('replace' => 'replace', 'type' => 'all')
        );

        $data['str_replace test 1'] = array(
            'This is a simple test I wrote by myself',
            array('type' => 'str', 'replace' => 'I wrote', 'match' => 'written')
        );

        $data['str_replace test 2'] = array(
            'This is a simple test written to myself',
            array('type' => 'str', 'replace' => 'to', 'match' => 'by')
        );

        $data['strtr test 1'] = array(
            'This#is#a#simple#test#written#by#myself',
            array('type' => 'str', 'replace' => array('#'), 'match' => array(' '))
        );

        $data['strtr test 2'] = array(
            'This is a simple test written to moself',
            array('type' => 'str', 'replace' => array('t', 'o'), 'match' => array('b', 'y'))
        );

        $data['preg replacement 1'] = array(
            'This is a simple test I wrote by myself',
            array('replace' => 'I wrote', 'match' => '/written/')
        );

        $data['preg replacement 2'] = array(
            'This is a simple test I wrote to myself',
            array('replace' => array('to', 'I wrote'), 'match' => array('/by/', '/written/'))
        );

        return $data;
    }

    /**
     * @dataProvider dataReplace
     */
    public function testReplace($expected, $args)
    {
        $replacement = $this->getReplacement($args);

        $this->assertSame($expected, $replacement->replace($this->text));
    }

    /**
     * @expectedException RuntimeException
     */
    public function testSplitReplacement()
    {
        $replacement = $this->getReplacement(array('type' => 'split', 'replace' => ''));
        $replacement->replace('');
    }
}