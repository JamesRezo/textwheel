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

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;

/**
 * TextWheel Factory.
 */
class Factory
{
    /**
     * Checks if a concrete class of a type exists.
     *
     * @param  string $type   a type of rule
     *
     * @return string|boolean a class name if exists or false
     */
    protected static function checkRuleType($type)
    {
        static $classes = array(
            'preg' => 'TextWheel\Rule\PregRule',
            'all' => 'TextWheel\Rule\AllRule',
            'split' => 'TextWheel\Rule\SplitRule',
            'str' => 'TextWheel\Rule\StrRule',
        );

        return array_key_exists($type, $classes) ? $classes[$type] : false;
    }

    /**
     * Creates a set of Condition object.
     *
     * @param array $args Properties of the rule
     *
     * @throws InvalidArgumentException if more than one condition defined
     *
     * @return ConditionInterface[] The Condition object list
     */
    public static function createConditions($args)
    {
        static $conditions = array(
            'if_chars' => 'TextWheel\Condition\CharsCondition',
            'if_match' => 'TextWheel\Condition\MatchCondition',
            'if_str' => 'TextWheel\Condition\StrCondition',
            'if_stri' => 'TextWheel\Condition\StriCondition',
        );

        $set = array();

        if ($condition = array_intersect_key($args, $conditions)) {
            foreach ($condition as $key => $value) {
                if ($key == 'if_str') {
                    # optimization: strpos or stripos?
                    if (strtolower($value) !== strtoupper($value)) {
                        $key = 'if_stri';
                    }
                }
                $class = $conditions[$key];
                $set[] = new $class($value);
            }
        }
        ksort($set, SORT_STRING);

        return $set;
    }

    /**
     * Creates a Rule object.
     *
     * @param  string $name  The name of the rule
     * @param  array  $args  Properties of the rule
     *
     * @return RuleInterface [description]
     */
    public static function createRule($name, array $args)
    {
        $type = isset($args['type']) ? $args['type'] : 'preg';
        $class = self::checkRuleType($type);

        if (!class_exists($class)) {
            throw new \Exception('No such a Rule type.');
        }

        return new $class($name, $args);
    }

    /**
     * file finder : can be overloaded in order to use application dependant
     * path find method
     *
     * @param string $file
     * @param string $path
     * @return string
     */
    public static function findFile($file, $path = '')
    {
        static $defaultPath;

        // absolute file path ?
        if (file_exists($file)) {
            return $file;
        }

        // file embed with texwheels, relative to calling ruleset
        if ($path and file_exists($f = $path . $file)) {
            return $f;
        }

        // textwheel default path ?
        if (!$defaultPath) {
            $defaultPath = __DIR__ . '/../wheels/';
        }
        if (file_exists($f = $defaultPath . $file)) {
            return $f;
        }

        return false;
    }
    
    /**
     * Load a yaml file describing rules.
     *
     * @param string $file
     * @param string $default_path
     *
     * @return array
     */
    public static function loadFile($file, $defaultPath = '')
    {
        if (!preg_match(',[.]ya?ml$,i', $file)
          // external rules
          or !$file = self::findFile($file, $defaultPath)) {
            return array();
        }

        $yaml = new Parser();

        try {
            $rules = $yaml->parse(file_get_contents($file));
        } catch (ParseException $e) {
            printf("Unable to parse the YAML string: %s", $e->getMessage());
        }

        if (is_null($rules)) {
            $rules = array();
        }

        // if a php file with same name exists
        // include it as it contains callback functions
        if ($f = preg_replace(',[.]ya?ml$,i', '.php', $file)
        and file_exists($f)) {
            $rules[] = array('require' => $f, 'priority' => -1000);
        }
        return $rules;
    }

    /**
     * public static loader
     * can be overloaded to use memoization
     *
     * @param array $ruleset
     * @param string $callback
     * @param string $class
     * @return class
     */
    public static function getRuleSet($ruleset, $callback = '', $class = 'TextWheel\Rule\RuleSet')
    {
        $ruleset = new $class($ruleset);
        if ($callback) {
            $callback($ruleset);
        }

        return $ruleset;
    }
}
