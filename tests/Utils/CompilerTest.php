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
use TextWheel\Utils\Compiler;

/**
 * Compiler tests.
 *
 * @author James Hautot <james@rezo.net>
 */
class CompilerTest extends TestCase
{
    public function dataCompiler()
    {
        require_once __DIR__ . '/../fixtures/' . 'dataReplace.php';
        require_once __DIR__ . '/../fixtures/' . 'dataCallbackReplace.php';

        return array_merge(dataReplace(), dataCallbackReplace());
    }

    /**
     * @dataProvider dataCompiler
     */
    public function testCompiler($expected, $args)
    {
        $this->requireFile('split_test.php');
        $this->requireFile('split_test2.php');

        $compiler = new Compiler();

        $code = $compiler->compile($this->getReplacement($args));
        $test = create_function('$text', $code);

        $this->assertSame($expected, $test($this->text));
    }
}
