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
 * apply() Rule tests.
 *
 * @author James Hautot <james@rezo.net>
 */
class ApplyRuleTest extends TestCase
{
    public function dataNominalCase()
    {
        $data = array();

        $data['Simple Preg Rule'] = array(
            'This is a simple text written by myself',
            $this->minimalArguments(),
        );

        $data['Preg Rule With One Condition 1'] = array(
            $this->text,
            array_merge(array('if_chars' => '#'), $this->minimalArguments()),
        );

        $data['Preg Rule With One Condition 2'] = array(
            'This is a simple text written by myself',
            array_merge(array('if_match' => '/^[A-Z]/'), $this->minimalArguments()),
        );

        $data['Preg Rule With 2 Conditions 1'] = array(
            'This is a simple text written by myself',
            array_merge(array('if_match' => '/^[A-Z]/', 'if_str' => ' '), $this->minimalArguments()),
        );

        $data['Preg Rule With 2 Conditions 2'] = array(
            $this->text,
            array_merge(array('if_match' => '/^[A-Z]/', 'if_str' => '#'), $this->minimalArguments()),
        );

        return $data;
    }

    /**
     * @dataProvider dataNominalCase
     */
    public function testNominalCase($expected, $args)
    {
        $rule = $this->getRule($args);
        $this->assertSame($expected, $rule->apply($this->text));
    }
}
