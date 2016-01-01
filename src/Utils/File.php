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

namespace TextWheel\Utils;

use TextWheel\Utils\Parser;

/**
 * File utilities.
 */
class File
{
    /**
     * File finder.
     *
     * Can be overloaded in order to use application dependant
     * path find method
     *
     * @param string $file
     * @param string $path
     *
     * @return string
     */
    private static function find($file, $path = '')
    {
        // absolute file path ?
        if (file_exists($file)) {
            return $file;
        }

        // file embed with texwheels, relative to calling ruleset
        if ($path and file_exists($path . $file)) {
            return $path . $file;
        }

        return false;
    }
    
    private static function getParser($file)
    {
        static $types = array(
            //array(',[.]php$,i',   'TextWheel\Utils\Parser\Php'),
            array(',[.]ya?ml$,i', 'TextWheel\Utils\Parser\Yaml'),
            array(',[.]json$,i',  'TextWheel\Utils\Parser\Json'),
            //array(',[.]xml$,i',   'TextWheel\Utils\Parser\Xml'),
        );

        foreach ($types as $value) {
            if (preg_match($value[0], $file)) {
                return $value[1];
            }
        }

        return false;
    }

    public static function getArray($file, $path = '')
    {
        $rules = array();

        if ($file = self::find($file, $path) and $parser = self::getParser($file)) {
            $content = file_get_contents($file);
            $rules = $parser::parse($content);
            if (is_null($rules)) {
                $rules = array();
            }

            #recursive rules
            $path = dirname($file) . '/';
            foreach ($rules as $name => $rule) {
                $file = $rule['replace'];
                if (is_string($file) and (isset($rule['is_wheel']) and $rule['is_wheel'])) {
                    $rules[$name]['replace'] = self::getArray($file, $path);
                }
            }
        }

        return $rules;
    }
}
