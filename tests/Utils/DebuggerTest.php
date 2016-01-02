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

namespace TextWheel\Test\Utils;

use TextWheel\Test\TestCase;
use TextWheel\Utils\Debugger;

/**
 * Debugger tests.
 *
 * @author James Hautot <james@rezo.net>
 */
class DebuggerTest extends TestCase
{
    public function dataDebugger()
    {
        $data = array();

        $data['hello.yml'] = array('hello.yml');
        $data['all.yml'] = array('all.yml');

        return $data;
    }

    /**
     * @dataProvider dataDebugger
     */
    public function testDebugger($file)
    {
        $debugger = new Debugger($this->fixtures . $file);
        $text = $debugger->process($this->text);
        $results = $debugger->getDebugProcess();

        $this->assertTrue($results['total'] > 0);
    }
}
