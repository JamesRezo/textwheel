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

/**
 * Debug TextWheel Object.
 */
class Debugger extends TextWheel
{
    protected $t; #tableaux des temps
    protected $tu; #tableaux des temps (rules utilises)
    protected $tnu; #tableaux des temps (rules non utilises)
    protected $u; #compteur des rules utiles
    protected $w; #compteur des rules appliques

    /**
     * Timer for profiling.
     *
     * @staticvar int $time
     * @param string $t
     * @param bool $raw
     * @return int/strinf
     */
    protected function timer($t = 'rien', $raw = false)
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
        if (!isset($time[$t])) {
            $time[$t] = $a + $b;
        } else {
            $p = ($a + $b - $time[$t]) * 1000;
            unset($time[$t]);
            if ($raw) {
                return $p;
            }
            if ($p < 1000) {
                $s = '';
            } else {
                $s = sprintf("%d ", $x = floor($p/1000));
                $p -= ($x * 1000);
            }

            return $s . sprintf("%.3f ms", $p);
        }
    }

    /**
     * Apply all rules of RuleSet to a text
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
            if (!isset($this->u[$name])) {
                $this->u[$name] = 0;
            }
            $before = $text;
            $text = $rule->apply($text);
            if (!isset($this->w[$name])) {
                $this->w[$name] = 0;
            }
            $this->w[$name] = $this->w[$name] + 1; # nombre de fois appliquee
            
            $v = $this->timer($name, true); # timer
            if (!isset($this->t[$name])) {
                $this->t[$name] = 0;
            }
            $this->t[$name] = $this->t[$name] + $v;

            if ($text !== $before) {
                $this->u[$name] = $this->u[$name] + 1; # nombre de fois utile
                if (!isset($this->tu[$name])) {
                    $this->tu[$name] = 0;
                }
                $this->tu[$name] = $this->tu[$name] + $v;
            } else {
                if (!isset($this->tnu[$name])) {
                    $this->tnu[$name] = 0;
                }
                $this->tnu[$name] = $this->tnu[$name] + $v;
            }
        }

        return $text;
    }

    public function getDebugProcess()
    {
        $total = 0;
        $results = array();

        if (isset($this->t)) {
            $time = array_flip(array_map('strval', $this->t));
            krsort($time);

            foreach ($time as $t => $r) {
                $applications = intval($this->u[$r]);
                $total += $t;
                if (intval($t * 10)) {
                    $nu = intval($this->w[$r]) - $applications;
                    $profile = array(
                        'time' => number_format(round($t * 10) / 10, 1),
                        'rule' => $r,
                        'application' => $applications . '/' . intval($this->w[$r]),
                        'time/used' => ($applications ? number_format(round($this->tu[$r] / $applications * 100) / 100, 2) : ''),
                        'time/unused' => ($nu ? number_format(round($this->tnu[$r] / $nu * 100) / 100, 2) : ''),
                    );
                    $results[] = $profile;
                }
            }
        }

        return array('total' => $total, 'results' =>$results);
    }
}
