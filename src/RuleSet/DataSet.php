<?php

/*
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

namespace TextWheel\RuleSet;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;

abstract class DataSet
{
    /**
     * file finder : can be overloaded in order to use application dependant
     * path find method
     *
     * @param string $file
     * @param string $path
     * @return string
     */
    protected function findFile($file, $path = '')
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
    protected function loadFile($file, $defaultPath = '')
    {
        if (!preg_match(',[.]ya?ml$,i', $file)
          // external rules
          or !$file = $this->findFile($file, $defaultPath)) {
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
