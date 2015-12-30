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
use TextWheel\Factory;
use TextWheel\Replacement\Wheel;

/**
 * The Main object of the libray.
 */
class TextWheel
{
    /** @var Wheel The Rules */
    protected $ruleset;

    /**
     * Base TextWheel Contructor.
     *
     * @param string|array $ruleset a file or an array of rules
     */
    public function __construct($ruleset)
    {
        if (is_file($ruleset)) {
            $ruleset = $this->loadFile($ruleset);
        }

        if (!is_array($ruleset)) {
            throw new \InvalidArgumentException("Error Processing Request", 1);
        }

        $name = isset($ruleset['name']) ? $ruleset['name'] : '';

        $this->ruleset = new Wheel($name, $ruleset);
    }

    /**
     * Process all rules of RuleSet to a text.
     *
     * @param  string $text The input text
     *
     * @return string       The output text
     */
    public function process($text)
    {
        return $this->ruleset->apply($text);
    }

    /**
     * file finder : can be overloaded in order to use application dependant
     * path find method
     *
     * @param string $file
     * @param string $path
     * @return string
     */
    private function findFile($file, $path = '')
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
    private function loadFile($file, $defaultPath = '')
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
