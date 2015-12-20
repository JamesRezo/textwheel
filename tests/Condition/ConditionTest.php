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

namespace TextWheel\Test\Condition;

use TextWheel\Test\TestCase;

/**
 * Condition tests.
 *
 * @author James Hautot <james@rezo.net>
 */
class ConditionTest extends TestCase
{
    public function dataAppliesTo()
    {
        $data = array();

        $data['Matches a Pattern'] = array(
            true,
            array('if_match' => '/by myself/'),
        );

        $data['Does not match a Pattern'] = array(
            false,
            array('if_match' => '/by someone else/'),
        );

        $data['Has listed chars'] = array(
            true,
            array('if_chars' => 'aeiy'),
        );

        $data['Does not have any listed chars'] = array(
            false,
            array('if_chars' => 'ou'),
        );

        $data['Contains a string'] = array(
            true,
            array('if_str' => 'myself'),
        );

        $data['Does not contain a string'] = array(
            false,
            array('if_str' => 'someone else'),
        );

        $data['Does not contain an EOL'] = array(
            false,
            array('if_str' => "\n"),
        );

        $data['Has listed chars but does not contain an EOL'] = array(
            false,
            array('if_str' => "\n", 'if_chars' => 'aeiy'),
        );

        $data['Has listed chars and contains a string'] = array(
            true,
            array('if_str' => 'myself', 'if_chars' => 'aeiy'),
        );

        return $data;
    }

    /**
     * @dataProvider dataAppliesTo
     */
    public function testAppliesTo($expected, $args)
    {
        $conditions = $this->getConditions($args);

        $pass = true;
        foreach ($conditions as $condition) {
            $pass = ($pass and $condition->appliesTo($this->text));
        }
        $this->assertSame($expected, $pass);
    }
}
