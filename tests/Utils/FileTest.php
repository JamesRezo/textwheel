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
use TextWheel\Utils\File;

/**
 * File tests.
 *
 * @author James Hautot <james@rezo.net>
 */
class FileTest extends TestCase
{
    public function testNoFileFound()
    {
        $this->assertEmpty(File::getArray('doesnotexist'));
    }

    public function testNoParserFound()
    {
        $this->assertEmpty(File::getArray($this->fixtures . 'testfile'));
    }

    public function testBadFormatFound()
    {
        $this->assertEmpty(File::getArray($this->fixtures . 'bad.json'));
    }

    public function testJsonRules()
    {
        $this->assertEquals(
            array('hello' => array(
                'type' => 'str',
                'match' => 'input',
                'replace' => 'output',
            )),
            File::getArray($this->fixtures . 'hello.json')
        );
    }

    public function testYamlRules()
    {
        $this->assertEquals(
            array('hello' => array(
                'type' => 'str',
                'match' => 'input',
                'replace' => 'output',
            )),
            File::getArray($this->fixtures . 'hello.yml')
        );
    }

    public function testRecursiveRules()
    {
        $this->assertEquals(
            array('replace_preg_wheel_inc' => array(
                'match' => '/in(put)/',
                'replace' => array(
                    0 => array(
                        'match' => 'e',
                        'type'=> 'str',
                        'replace' => 'i',
                    ),
                ),
                'is_wheel' => 'Y',
                'pick_match' => 1,
            )),
            File::getArray($this->fixtures . 'textwheel.yaml')
        );
    }
}
