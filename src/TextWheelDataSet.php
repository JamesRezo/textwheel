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

namespace TextWheel;

abstract class TextWheelDataSet
{
    # list of data
    protected $data = array();

    /**
     * file finder : can be overloaded in order to use application dependant
     * path find method
     *
     * @param string $file
     * @param string $path
     * @return string
     */
    protected function findFile(&$file, $path = '')
    {
        static $default_path;

        // absolute file path ?
        if (file_exists($file)) {
            return $file;
        }

        // file embed with texwheels, relative to calling ruleset
        if ($path and file_exists($f = $path.$file)) {
            return $f;
        }

        // textwheel default path ?
        if (!$default_path) {
            $default_path = dirname(__FILE__).'/../wheels/';
        }
        if (file_exists($f = $default_path.$file)) {
            return $f;
        }

        return false;
    }
    
    /**
     * Load a yaml file describing data
     * @param string $file
     * @param string $default_path
     * @return array
     */
    protected function loadFile(&$file, $default_path = '')
    {
        if (!preg_match(',[.]yaml$,i', $file)
          // external rules
          or !$file = $this->findFile($file, $default_path)) {
            return array();
        }

        defined('_YAML_EVAL_PHP') || define('_YAML_EVAL_PHP', false);
        if (!function_exists('yaml_decode')) {
            if (function_exists('include_spip')) {
                include_spip('inc/yaml-mini');
            } else {
                require_once dirname(__FILE__).'/../inc/yaml.php';
            }
        }
        $dataset = yaml_decode(file_get_contents($file));

        if (is_null($dataset)) {
            $dataset = array();
        }
#           throw new DomainException('yaml file is empty, unreadable or badly formed: '.$file.var_export($dataset,true));

        // if a php file with same name exists
        // include it as it contains callback functions
        if ($f = preg_replace(',[.]yaml$,i', '.php', $file)
        and file_exists($f)) {
            $dataset[] = array('require' => $f, 'priority' => -1000);
        }
        return $dataset;
    }
}
