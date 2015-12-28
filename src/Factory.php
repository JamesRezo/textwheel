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
use TextWheel\Rule\Rule;
use TextWheel\Rule\RuleSet;
use TextWheel\Replacement\Wheel;

/**
 * TextWheel Factory.
 */
class Factory
{
    /**
     * Creates a Replacement object.
     *
     * @param array $args Properties of the rule
     *
     * @return ReplacementInterface a Replacement Object (Identity as fallback)
     */
    public static function createReplacement($args, $name = '0')
    {
        static $replacements = array(
            'preg' => 'PregReplacement',
            'all' => 'AllReplacement',
            'split' => 'SplitReplacement',
            'str' => 'StrReplacement',
            'preg_cb' => 'CallbackPregReplacement',
            'all_cb' => 'CallbackAllReplacement',
            'split_cb' => 'CallbackSplitReplacement',
            'str_cb' => 'CallbackStrReplacement',
        );

        static $properties = array(
            'type' => 'preg',
            'replace' => null,
            'match' => '',
            'glue' => null,
            'is_callback' => false,
            'create_replace' => false,
            'is_wheel' => false,
            'pick_match' => 0,
        );

        $args = array_merge($properties, $args);
        $replacement = array_intersect_key($args, $properties);

        if ((bool) $replacement['is_wheel']) {
            $wheel = new Wheel($name, $args);
            foreach ($replacement['replace'] as $subwheel) {
                $wheel->add(self::createReplacement($subwheel));
            }

            $replacement['replace'] = function ($matches) use ($wheel, $replacement) {
                $matches = ('preg' !== $replacement['type'] or !isset($replacement['match'])) ?
                    $matches :
                    $matches[intval($replacement['pick_match'])];
                return $wheel->apply($matches);
            };

            $replacement['is_callback'] = true;
            $replacement['create_replace'] = false;
        }

        if ((bool) $replacement['create_replace']) {
            $replacement['is_callback'] = true;
            $code = $replacement['replace'];
            //$replacement['replace'] = create_function('$matches', $code);
            $replacement['replace'] = function ($matches) use ($code) {
                return eval($code); //TODO eval() is huge.
            };
        }

        if (!isset($replacement['replace'])) {
            $replacement['type'] = '';
        }
        
        $replacementClass = 'TextWheel\Replacement\\';
        if ($replacement['is_callback']) {
            $replacement['type'] = preg_replace('/^(preg|all|split|str)(_cb)?$/', '$1_cb', $replacement['type']);
        }

        $replacementClass .= array_key_exists($replacement['type'], $replacements) ?
            $replacements[$replacement['type']] :
            'IdentityReplacement'
        ;

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
                $set[$key] = new $class($value);
            }
            ksort($set, SORT_STRING);
        }

        return array_values($set);
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
}
