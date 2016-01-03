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

namespace TextWheel;

use TextWheel\Replacement\Wheel;

/**
 * TextWheel Factory.
 */
class Factory
{
    /** @var array Condition classes */
    private static $conditions = array(
        'if_chars' => 'TextWheel\Condition\CharsCondition',
        'if_match' => 'TextWheel\Condition\MatchCondition',
        'if_str' => 'TextWheel\Condition\StrCondition',
        'if_stri' => 'TextWheel\Condition\StriCondition',
    );

    /** @var array Replacement classes */
    private static $replacements = array(
        'preg' => 'TextWheel\Replacement\PregReplacement',
        'all' => 'TextWheel\Replacement\AllReplacement',
        'split' => 'TextWheel\Replacement\SplitReplacement',
        'str' => 'TextWheel\Replacement\StrReplacement',
        'preg_cb' => 'TextWheel\Replacement\CallbackPregReplacement',
        'all_cb' => 'TextWheel\Replacement\CallbackAllReplacement',
        'split_cb' => 'TextWheel\Replacement\CallbackSplitReplacement',
        'str_cb' => 'TextWheel\Replacement\CallbackStrReplacement',
    );

    /**
     * Builds the Full Class Name of a Replacement
     *
     * @param  string  $type         The type of the rule
     * @param  boolean $isCallback   true if replace is a callback function
     *
     * @return string  Class name
     */
    private static function buildReplacementClass($type, $isCallback)
    {
        if ($isCallback) {
            $type = preg_replace('/^(preg|all|split|str)(_cb)?$/', '$1_cb', $type);
        }

        $replacementClass = array_key_exists($type, self::$replacements) ?
            self::$replacements[$type] :
            'TextWheel\Replacement\IdentityReplacement'
        ;

        return $replacementClass;
    }

    /**
     * Gets a list of Condition set by properties.
     *
     * @param  array $args Properties of the rule
     *
     * @return array       Condition properties of the rule
     */
    private static function getConditionList(array $args)
    {
        return array_intersect_key($args, self::$conditions);
    }

    /**
     * Builds a Wheeled Replacement.
     *
     * @param  array  $replacement Rule properties
     * @param  string $name        Optional name of the wheel
     *
     * @return array               Rule properties
     */
    private static function buildWheeledReplacement(array $replacement, $name = '')
    {
        $wheel = new Wheel($name, $replacement);
        foreach ($replacement['replace'] as $subname => $subwheel) {
            $wheel->add(self::createReplacement($subwheel, $subname));
        }

        $replacement['replace'] = function ($matches) use ($wheel, $replacement) {
            $matches = ('preg' !== $replacement['type'] or !isset($replacement['match'])) ?
                $matches :
                $matches[intval($replacement['pick_match'])];
            return $wheel->apply($matches);
        };

        $replacement['is_callback'] = true;
        $replacement['create_replace'] = false;

        return $replacement;
    }

    /**
     * Creates a Replacement object.
     *
     * @param array  $args Properties of the rule
     * @param string $name The name of the rule
     *
     * @return ReplacementInterface a Replacement Object (Identity as fallback)
     */
    public static function createReplacement($args, $name = '')
    {
        static $defaultProperties = array(
            'type' => 'preg',
            'replace' => null,
            'match' => '',
            'glue' => null,
            'is_callback' => false,
            'create_replace' => false,
            'is_wheel' => false,
            'pick_match' => 0,
            'priority' => 0,
            'disabled' => false,
        );

        $args = array_merge($defaultProperties, $args);
        $replacement = array_intersect_key($args, $defaultProperties);
        $replacement = array_merge($replacement, self::getConditionList($args));

        if ((bool) $replacement['is_wheel']) {
            $replacement = self::buildWheeledReplacement($replacement);
        }

        if ((bool) $replacement['create_replace']) {
            $replacement['is_callback'] = true;
            $code = $replacement['replace'];
            $replacement['replace'] = create_function('$matches', $code);
        }

        if (!isset($replacement['replace'])) {
            $replacement['type'] = '';
        }
 
        $replacementClass = self::buildReplacementClass($replacement['type'], $replacement['is_callback']);

        return new $replacementClass($name, $replacement);
    }

    /**
     * Creates a set of Condition object.
     *
     * @param array $args Properties of the rule
     *
     * @return ConditionInterface[] The Condition object list
     */
    public static function createConditions($args)
    {
        $set = array();

        if ($condition = self::getConditionList($args)) {
            foreach ($condition as $key => $value) {
                if ($key == 'if_str') {
                    # optimization: strpos or stripos?
                    if (strtolower($value) !== strtoupper($value)) {
                        $key = 'if_stri';
                    }
                }
                $class = self::$conditions[$key];
                $set[$key] = new $class($value);
            }
            ksort($set, SORT_STRING);
        }

        return array_values($set);
    }
}
