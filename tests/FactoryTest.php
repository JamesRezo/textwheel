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

use TextWheel\Test\TestCase;
use TextWheel\Factory;

#use TextWheel\Replacement\IdendityReplacement;

/**
 * Replacement tests.
 *
 * @author James Hautot <james@rezo.net>
 */
class FactoryTest extends TestCase
{
    public function testNoCondition()
    {
        $this->assertNull(Factory::createCondition(array()));
    }

    public function testUnknownCondition()
    {
        $this->assertNull(Factory::createCondition(array('unknown' => '')));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testTooMuchConditions()
    {
        $condition = $this->getCondition(array('if_str' => "\n", 'if_chars' => 'aeiy'));
    }
}
