<?php

/**
 * TextWheel 0.1.
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
 */

namespace TextWheel\Utils;

use TextWheel\TextWheel;

/**
 * Debug TextWheel Object.
 */
class Debugger extends TextWheel
{
    /** @var array times */
    protected $times;

    /** @var array used rules times */
    protected $usedRuleTimes;

    /** @var array unused rules times */
    protected $unusedRuleTimes;

    /** @var array used rules counter */
    protected $usedRuleCounter;

    /** @var array applied rules counter */
    protected $appliedRuleCounter;

    /**
     * Timer for profiling.
     *
     * @static integer $time
     *
     * @param string $ruleName The name of the Rule
     * @param bool   $raw
     *
     * @return int/strinf
     */
    protected function timer($ruleName, $raw = false)
    {
        static $time;

        $a = time();
        $b = microtime();
        // microtime peut contenir les microsecondes et le temps
        $b = explode(' ', $b);
        if (count($b) == 2) {
            $a = end($b);
        } // plus precis !
        $b = reset($b);
        if (!isset($time[$ruleName])) {
            $time[$ruleName] = $a + $b;
        } else {
            $p = ($a + $b - $time[$ruleName]) * 1000;
            unset($time[$ruleName]);
            if ($raw) {
                return $p;
            }
            if ($p < 1000) {
                $s = '';
            } else {
                $s = sprintf('%d ', $x = floor($p / 1000));
                $p -= ($x * 1000);
            }

            return $s.sprintf('%.3f ms', $p);
        }
    }

    /**
     * Apply all rules of RuleSet to a text.
     *
     * @param string $text
     *
     * @return string
     */
    public function process($text)
    {
        foreach ($this->ruleset as $rule) {
            $name = $rule->getName();
            if (is_int($name)) {
                $name .= ' '.$rule->getMatch();
            }

            $this->timer($name);
            if (!isset($this->usedRuleCounter[$name])) {
                $this->usedRuleCounter[$name] = 0;
            }
            $before = $text;
            $text = $rule->apply($text);
            if (!isset($this->appliedRuleCounter[$name])) {
                $this->appliedRuleCounter[$name] = 0;
            }
            $this->appliedRuleCounter[$name] = $this->appliedRuleCounter[$name] + 1; // nombre de fois appliquee

            $v = $this->timer($name, true); // timer
            if (!isset($this->times[$name])) {
                $this->times[$name] = 0;
            }
            $this->times[$name] = $this->times[$name] + $v;

            if ($text !== $before) {
                $this->usedRuleCounter[$name] = $this->usedRuleCounter[$name] + 1; // nombre de fois utile
                if (!isset($this->usedRuleTimes[$name])) {
                    $this->usedRuleTimes[$name] = 0;
                }
                $this->usedRuleTimes[$name] = $this->usedRuleTimes[$name] + $v;
            } else {
                if (!isset($this->unusedRuleTimes[$name])) {
                    $this->unusedRuleTimes[$name] = 0;
                }
                $this->unusedRuleTimes[$name] = $this->unusedRuleTimes[$name] + $v;
            }
        }

        return $text;
    }

    public function getDebugProcess()
    {
        $total = 0;
        $results = array();

        if (isset($this->times)) {
            $times = array_flip(array_map('strval', $this->times));
            krsort($times);

            foreach ($times as $t => $rule) {
                $applications = intval($this->usedRuleCounter[$rule]);
                $total += $t;
                if (intval($t * 10)) {
                    $nu = intval($this->appliedRuleCounter[$rule]) - $applications;
                    $profile = array(
                        'time' => number_format(round($t * 10) / 10, 1),
                        'rule' => $rule,
                        'application' => $applications.'/'.intval($this->appliedRuleCounter[$rule]),
                        'time/used' => ($applications ? number_format(round($this->usedRuleTimes[$rule] / $applications * 100) / 100, 2) : ''),
                        'time/unused' => ($nu ? number_format(round($this->unusedRuleTimes[$rule] / $nu * 100) / 100, 2) : ''),
                    );
                    $results[] = $profile;
                }
            }
        }

        return array('total' => $total, 'results' => $results);
    }
}
