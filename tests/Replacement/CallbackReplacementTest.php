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
class CallbackReplacementTest extends TestCase
{
    public function dataReplace()
    {
        $data = array();

        $data['Callback All with native function'] = array(
            'Guvf vf n fvzcyr grfg jevggra ol zlfrys',
            array('replace' => 'str_rot13', 'type' => 'all', 'is_callback' => 1)
        );

        $data['Callback Split with user defined function'] = array(
            'Thissplit issplit asplit simplesplit testsplit writtensplit bysplit myselfsplit',
            array('replace' => 'split_test', 'match' => ' ', 'type' => 'split', 'is_callback' => 1)
        );

        $data['Callback Split with glue'] = array(
            'Thissplit#issplit#asplit#simplesplit#testsplit#writtensplit#bysplit#myselfsplit',
            array('replace' => 'split_test', 'match' => ' ', 'type' => 'split', 'is_callback' => 1, 'glue' => '#')
        );

        $data['Callback with Object static method'] = array(
            'This is a simple test written by myselfFooBar',
            array('replace' => 'Foo::Bar', 'type' => 'all', 'is_callback' => 1)
        );

        $data['Callback with namespaced method'] = array(
            'This is a simple test written by myselfsplit',
            array('replace' => 'split_test\split_test', 'type' => 'all', 'is_callback' => 1)
        );

        $data['Callback Str with user defined function'] = array(
            'This splitis splita splitsimple splittest splitwritten splitby splitmyself',
            array('replace' => 'split_test', 'match' => ' ', 'type' => 'str', 'is_callback' => 1)
        );

        $data['Callback Preg with user defined function'] = array(
            'Guvf vf n fvzcyr grfg jevggra ol zlfrys',
            array('match' => '/(\w+)/', 'replace' => 'preg_test', 'is_callback' => 1)
        );

        $data['Callback with Object static method'] = array(
            '(This) (is) (a) (simple) (test) (written) (by) (myself)',
            array('match' => '/(\w+)/', 'replace' => 'Foo::Baz', 'is_callback' => 1)
        );

        return $data;
    }

    /**
     * @dataProvider dataReplace
     */
    public function testReplace($expected, $args)
    {
        $this->requireFile('split_test.php');
        $this->requireFile('split_test2.php');

        $replacement = $this->getReplacement($args);

        $this->assertSame($expected, $replacement->replace($this->text));
    }
}
