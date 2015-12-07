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
        $this->assertEmpty(Factory::createConditions(array()));
    }

    public function testUnknownCondition()
    {
        $this->assertEmpty(Factory::createConditions(array('unknown' => '')));
    }

    public function dataConditionList()
    {
        $data = array();

        $data['One condition'] = array(
            array('TextWheel\Condition\CharsCondition'),
            array('if_chars' => '#')
        );

        $data['Several conditions sorted'] = array(
            array('TextWheel\Condition\CharsCondition', 'TextWheel\Condition\StriCondition'),
            array('if_stri' => '@', 'if_chars' => '#')
        );

        $data['str optimization'] = array(
            array('TextWheel\Condition\StriCondition'),
            array('if_str' => 'alphanumeric')
        );

        return $data;
    }

    /**
     * @dataProvider dataConditionList
     */
    public function testConditionList($expected, $args)
    {
        $conditions = Factory::createConditions($args);

        foreach ($conditions as $index => $condition) {
            $conditions[$index] = get_class($condition);
        }

        $this->assertSame($expected, $conditions);
    }
}
