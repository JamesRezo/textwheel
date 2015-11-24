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

class TextWheel
{
    protected $ruleset;
    protected static $subwheel = array();

    protected $compiled = array();

    /**
     * Constructor
     * @param TextWheelRuleSet $ruleset
     */
    public function __construct($ruleset = null)
    {
        $this->setRuleSet($ruleset);
    }

    /**
     * Set RuleSet
     * @param TextWheelRuleSet $ruleset
     */
    public function setRuleSet($ruleset)
    {
        if (!is_object($ruleset)) {
            $ruleset = new TextWheelRuleSet($ruleset);
        }
        $this->ruleset = $ruleset;
    }

    /**
     * Apply all rules of RuleSet to a text
     *
     * @param string $t
     * @return string
     */
    public function text($t)
    {
        $rules = & $this->ruleset->getRules();
        ## apply each in order
        foreach ($rules as $name => $rule) {
            #php4+php5

            $this->apply($rules[$name], $t);
        }
        #foreach ($this->rules as &$rule) #smarter &reference, but php5 only
        #	$this->apply($rule, $t);
        return $t;
    }

    private function export($x)
    {
        return addcslashes(var_export($x, true), "\n\r\t");
    }

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
            and $fun = $this->compiled[$rule->replace]) {
                $pre[] = "\n###\n## $name\n###\n" . $fun;
                preg_match(',function (\w+), ', $fun, $r);
                $rule->compilereplace = $r[1]; # ne pas modifier ->replace sinon on casse l'execution...
            }

            $r = "\t/* $name */\n";

            if ($rule->require) {
                $r .= "\t".'require_once '.TextWheel::export($rule->require).';'."\n";
            }
            if ($rule->if_str) {
                $r .= "\t".'if (strpos($t, '.TextWheel::export($rule->if_str).') === false)'."\n";
            }
            if ($rule->if_stri) {
                $r .= "\t".'if (stripos($t, '.TextWheel::export($rule->if_stri).') === false)'."\n";
            }
            if ($rule->if_match) {
                $r .= "\t".'if (preg_match('.TextWheel::export($rule->if_match).', $t))'."\n";
            }

            if ($rule->func_replace !== 'replace_identity') {
                $fun = 'TextWheel::'.$rule->func_replace;
                switch ($fun) {
                    case 'TextWheel::replace_all_cb':
                        $fun = $rule->replace; # trim()...
                        break;
                    case 'TextWheel::replace_preg':
                        $fun = 'preg_replace';
                        break;
                    case 'TextWheel::replace_str':
                        $fun = 'str_replace';
                        break;
                    case 'TextWheel::replace_preg_cb':
                        $fun = 'preg_replace_callback';
                        break;
                    default:
                        break;
                }
                $r .= "\t".'$t = '.$fun.'('.TextWheel::export($rule->match).', '.TextWheel::export($rule->replace).', $t);'."\n";
            }

            $comp[] = $r;
        }
        $code = join("\n", $comp);
        $code = 'function '.$b.'($t) {' . "\n". $code . "\n\treturn \$t;\n}\n\n";
        $code = join("\n", $pre) . $code;

        return $code;
    }


    /**
     * Get an internal global subwheel
     * read acces for annymous function only
     *
     * @param int $n
     * @return TextWheel
     */
    public static function &getSubWheel($n)
    {
        return TextWheel::$subwheel[$n];
    }

    /**
     * Create SubWheel (can be overriden in debug class)
     * @param TextWheelRuleset $rules
     * @return TextWheel
     */
    protected function &createSubWheel(&$rules)
    {
        $tw = new TextWheel($rules);
        return $tw;
    }

    /**
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

        # optimization: strpos or stripos?
        if (isset($rule->if_str)) {
            if (strtolower($rule->if_str) !== strtoupper($rule->if_str)) {
                $rule->if_stri = $rule->if_str;
                $rule->if_str = null;
            }
        }

        if ($rule->create_replace) {
            $compile = $rule->replace.'($t)';
            $rule->replace = create_function('$m', $rule->replace);
            $this->compiled[$rule->replace] = $compile;
            $rule->create_replace = false;
            $rule->is_callback = true;
        } elseif ($rule->is_wheel) {
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

        # optimization
        $rule->func_replace = '';
        if (isset($rule->replace)) {
            switch ($rule->type) {
                case 'all':
                    $rule->func_replace = 'replace_all';
                    break;
                case 'str':
                    $rule->func_replace = 'replace_str';
                    // test if quicker strtr usable
                    if (!$rule->is_callback
                        and is_array($rule->match) and is_array($rule->replace)
                        and $c = array_map('strlen', $rule->match)
                        and $c = array_unique($c)
                        and count($c)==1
                        and reset($c)==1
                        and $c = array_map('strlen', $rule->replace)
                        and $c = array_unique($c)
                        and count($c)==1
                        and reset($c)==1
                        ) {
                        $rule->match = implode('', $rule->match);
                        $rule->replace = implode('', $rule->replace);
                        $rule->func_replace = 'replace_strtr';
                    }
                    break;
                case 'split':
                    $rule->func_replace = 'replace_split';
                    $rule->match = array($rule->match,  is_null($rule->glue)?$rule->match:$rule->glue);
                    break;
                case 'preg':
                default:
                    $rule->func_replace = 'replace_preg';
                    break;
            }
            if ($rule->is_callback) {
                $rule->func_replace .= '_cb';
            }
        }
        if (!method_exists("TextWheel", $rule->func_replace)) {
            $rule->disabled = true;
            $rule->func_replace = 'replace_identity';
        }
        # /end
    }

    /**
     * Apply a rule to a text
     *
     * @param TextWheelRule $rule
     * @param string $t
     * @param int $count
     */
    protected function apply(&$rule, &$t, &$count = null)
    {
        if ($rule->disabled) {
            return;
        }

        if (isset($rule->if_chars) and (strpbrk($t, $rule->if_chars) === false)) {
            return;
        }

        if (isset($rule->if_match) and !preg_match($rule->if_match, $t)) {
            return;
        }

        // init rule before testing if_str / if_stri as they are optimized by initRule
        if (!isset($rule->func_replace)) {
            $this->initRule($rule);
        }

        if (isset($rule->if_str) and strpos($t, $rule->if_str) === false) {
            return;
        }

        if (isset($rule->if_stri) and stripos($t, $rule->if_stri) === false) {
            return;
        }

        $func = $rule->func_replace;
        TextWheel::$func($rule->match, $rule->replace, $t, $count);
    }

    /**
     * No Replacement function
     * fall back in case of unknown method for replacing
     * should be called max once per rule
     *
     * @param mixed $match
     * @param mixed $replace
     * @param string $t
     * @param int $count
     */
    protected static function replaceIdentity(&$match, &$replace, &$t, &$count)
    {
    }

    /**
     * Static replacement of All text
     * @param mixed $match
     * @param mixed $replace
     * @param string $t
     * @param int $count
     */
    protected static function replaceAll(&$match, &$replace, &$t, &$count)
    {
        # special case: replace $0 with $t
        #   replace: "A$0B" will surround the string with A..B
        #   replace: "$0$0" will repeat the string
        if (strpos($replace, '$0')!==false) {
            $t = str_replace('$0', $t, $replace);
        } else {
            $t = $replace;
        }
    }

    /**
     * Call back replacement of All text
     * @param mixed $match
     * @param mixed $replace
     * @param string $t
     * @param int $count
     */
    protected static function replaceAllCb(&$match, &$replace, &$t, &$count)
    {
        $t = $replace($t);
    }

    /**
     * Static string replacement
     *
     * @param mixed $match
     * @param mixed $replace
     * @param string $t
     * @param int $count
     */
    protected static function replaceStr(&$match, &$replace, &$t, &$count)
    {
        if (!is_string($match) or strpos($t, $match)!==false) {
            $t = str_replace($match, $replace, $t, $count);
        }
    }

    /**
     * Fast Static string replacement one char to one char
     *
     * @param mixed $match
     * @param mixed $replace
     * @param string $t
     * @param int $count
     */
    protected static function replaceStrtr(&$match, &$replace, &$t, &$count)
    {
        $t = strtr($t, $match, $replace);
    }

    /**
     * Callback string replacement
     *
     * @param mixed $match
     * @param mixed $replace
     * @param string $t
     * @param int $count
     */
    protected static function replaceStrCb(&$match, &$replace, &$t, &$count)
    {
        if (strpos($t, $match)!==false) {
            if (count($b = explode($match, $t)) > 1) {
                $t = join($replace($match), $b);
            }
        }
    }

    /**
     * Static Preg replacement
     *
     * @param mixed $match
     * @param mixed $replace
     * @param string $t
     * @param int $count
     * @throws Exception
     */
    protected static function replacePreg(&$match, &$replace, &$t, &$count)
    {
        $t = preg_replace($match, $replace, $t, -1, $count);
        if (is_null($t)) {
            throw new Exception('Memory error, increase pcre.backtrack_limit in php.ini');
        }
    }

    /**
     * Callback Preg replacement
     * @param mixed $match
     * @param mixed $replace
     * @param string $t
     * @param int $count
     * @throws Exception
     */
    protected static function replacePregCb(&$match, &$replace, &$t, &$count)
    {
        $t = preg_replace_callback($match, $replace, $t, -1, $count);
        if (is_null($t)) {
            throw new Exception('Memory error, increase pcre.backtrack_limit in php.ini');
        }
    }

    /**
     * Static split replacement : invalid
     * @param mixed $match
     * @param mixed $replace
     * @param string $t
     * @param int $count
     */
    protected static function replaceSplit(&$match, &$replace, &$t, &$count)
    {
        throw new InvalidArgumentException('split rule always needs a callback function as replace');
    }

    /**
     * Callback split replacement
     * @param array $match
     * @param mixed $replace
     * @param string $t
     * @param int $count
     */
    protected static function replaceSplitCb(&$match, &$replace, &$t, &$count)
    {
        $a = explode($match[0], $t);
        $t = join($match[1], array_map($replace, $a));
    }
}
