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

use TextWheel\TextWheel;
use TextWheel\Rule\RuleSet;

/**
 * The Main object of the libray.
 */
class Compiler extends TextWheel
{
    /** @var integer item to pick for sub-wheel replace */
    public $pick_match = 0;

    /** @var array Store for compiled code */
    protected $compiled = array();

    /**
     * [export description]
     *
     * @param  [type] $x [description]
     *
     * @return [type]    [description]
     */
    private function export($x)
    {
        return addcslashes(var_export($x, true), "\n\r\t");
    }

    /**
     * Compile the replacement and conditions in code.
     *
     * @param  string $b a Rule name
     *
     * @return string    The compiled code
     */
    public function compile($b = null)
    {
        $rules = & $this->ruleset->getRules();

        ## apply each in order
        $pre = array();
        $comp = array();

        foreach ($rules as $name => $rule) {
            $rule->name = $name;
            $this->initRule($rule);
            if (is_string($rule->replace)
                and isset($this->compiled[$rule->replace])
                and $fun = $this->compiled[$rule->replace]
            ) {
                $pre[] = "\n###\n## $name\n###\n" . $fun;
                preg_match(',function (\w+), ', $fun, $r);
                $rule->compilereplace = $r[1]; # ne pas modifier ->replace sinon on casse l'execution...
            }

            $r = "\t/* $name */\n";

            if ($rule->require) {
                $r .= "\t".'require_once '.$this->export($rule->require).';'."\n";
            }
            if ($rule->if_str) {
                $r .= "\t".'if (strpos($t, '.$this->export($rule->if_str).') === false)'."\n";
            }
            if ($rule->if_stri) {
                $r .= "\t".'if (stripos($t, '.$this->export($rule->if_stri).') === false)'."\n";
            }
            if ($rule->if_match) {
                $r .= "\t".'if (preg_match('.$this->export($rule->if_match).', $t))'."\n";
            }

            if ($rule->func_replace !== 'replaceIdentity') {
                $fun = 'TextWheel::'.$rule->func_replace;
                switch ($fun) {
                    case 'TextWheel::replaceAllCb':
                        $fun = $rule->replace; # trim()...
                        break;
                    case 'TextWheel::replacePreg':
                        $fun = 'preg_replace';
                        break;
                    case 'TextWheel::replaceStr':
                        $fun = 'str_replace';
                        break;
                    case 'TextWheel::replacePregCb':
                        $fun = 'preg_replace_callback';
                        break;
                    default:
                        break;
                }
                $r .= "\t".'$t = '.$fun.'('.$this->export($rule->match).', '.$this->export($rule->replace).', $t);'."\n";
            }

            $comp[] = $r;
        }
        $code = join("\n", $comp);
        $code = 'function '.$b.'($t) {' . "\n". $code . "\n\treturn \$t;\n}\n\n";
        $code = join("\n", $pre) . $code;

        return $code;
    }

    /**
     * TODO go to Factory as a Builder Pattern.
     *
     * Initializing a rule a first call
     * including file, creating function or wheel
     * optimizing tests
     *
     * @param TextWheelRule $rule
     */
    protected function initRule(&$rule)
    {
        # language specific
        if ($rule->require) {
            require_once $rule->require;
        }

        if ($rule->is_wheel) {
            $n = count(TextWheel::$subwheel);
            TextWheel::$subwheel[] = $this->createSubWheel($rule->replace);
            $var = '$m['.intval($rule->pick_match).']';
            if ($rule->type=='all' or $rule->type=='str' or $rule->type=='split' or !isset($rule->match)) {
                $var = '$m';
            }
            $code = 'return TextWheel::getSubWheel('.$n.')->text('.$var.');';
            $rule->replace = create_function('$m', $code);
            $cname = 'compiled_'.str_replace('-', '_', $rule->name);
            $compile = TextWheel::getSubWheel($n)->compile($cname);
            $this->compiled[$rule->replace] = $compile;
            $rule->is_wheel = false;
            $rule->is_callback = true;
        }
    }
}
