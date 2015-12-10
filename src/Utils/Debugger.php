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
    protected static $t; #tableaux des temps
    protected static $tu; #tableaux des temps (rules utilises)
    protected static $tnu; #tableaux des temps (rules non utilises)
    protected static $u; #compteur des rules utiles
    protected static $w; #compteur des rules appliques
    public static $total;

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
        $a=time();
        $b=microtime();
        // microtime peut contenir les microsecondes et le temps
        $b=explode(' ', $b);
        if (count($b)==2) {
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
                $p -= ($x*1000);
            }
            return $s . sprintf("%.3f ms", $p);
        }
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

            if (is_int($name)) {
                $name .= ' '.$rule->match;
            }
            $this->timer($name);
            $b = $t;
            $this->apply($rule, $t);
            TextWheelDebug::$w[$name] ++; # nombre de fois appliquee
            $v = $this->timer($name, true); # timer
            TextWheelDebug::$t[$name] += $v;
            if ($t !== $b) {
                TextWheelDebug::$u[$name] ++; # nombre de fois utile
                TextWheelDebug::$tu[$name] += $v;
            } else {
                TextWheelDebug::$tnu[$name] += $v;
            }
        }
        #foreach ($this->rules as &$rule) #smarter &reference, but php5 only
        #   $this->apply($rule, $t);
        return $t;
    }

    /**
     * Ouputs data stored for profiling/debuging purposes
     */
    public static function outputDebug()
    {
        if (isset(TextWheelDebug::$t)) {
            $time = array_flip(array_map('strval', TextWheelDebug::$t));
            krsort($time);
            echo "
            <div class='textwheeldebug'>
            <style type='text/css'>
                .textwheeldebug table { margin:1em 0; }
                .textwheeldebug th,.textwheeldebug td { padding-left: 15px }
                .textwheeldebug .prof-0 .number { padding-right: 60px }
                .textwheeldebug .prof-1 .number { padding-right: 30px }
                .textwheeldebug .prof-1 .name { padding-left: 30px }
                .textwheeldebug .prof-2 .name { padding-left: 60px }
                .textwheeldebug .zero { color:orange; }
                .textwheeldebug .number { text-align:right; }
                .textwheeldebug .strong { font-weight:bold; }
            </style>
            <table class='sortable'>
            <caption>Temps par rule</caption>
            <thead><tr><th>temps&nbsp;(ms)</th><th>rule</th><th>application</th><th>t/u&nbsp;(ms)</th><th>t/n-u&nbsp;(ms)</th></tr></thead>\n";
            $total = 0;
            foreach ($time as $t => $r) {
                $applications = intval(TextWheelDebug::$u[$r]);
                $total += $t;
                if (intval($t*10)) {
                    echo "<tr>
                    <td class='number strong'>".number_format(round($t*10)/10, 1)."</td><td> ".spip_htmlspecialchars($r)."</td>
                    <td"
                    . (!$applications ? " class='zero'" : "")
                    .">".$applications."/".intval(TextWheelDebug::$w[$r])."</td>
                    <td class='number'>".($applications?number_format(round(TextWheelDebug::$tu[$r]/$applications*100)/100, 2):"") ."</td>
                    <td class='number'>".(($nu = intval(TextWheelDebug::$w[$r])-$applications)?number_format(round(TextWheelDebug::$tnu[$r]/$nu*100)/100, 2):"") ."</td>
                    </tr>";
                }
            }
            echo "</table>\n";

            echo "
            <table>
            <caption>Temps total par rule</caption>
            <thead><tr><th>temps</th><th>rule</th></tr></thead>\n";
            ksort($GLOBALS['totaux']);
            TextWheelDebug::outputTotal($GLOBALS['totaux']);
            echo "</table>";
            # somme des temps des rules, ne tient pas compte des subwheels
            echo "<p>temps total rules: ".round($total)."&nbsp;ms</p>\n";
            echo "</div>\n";
        }
    }

    public static function outputTotal($liste, $profondeur = 0)
    {
        ksort($liste);
        foreach ($liste as $cause => $duree) {
            if (is_array($duree)) {
                TextWheelDebug::outputTotal($duree, $profondeur+1);
            } else {
                echo "<tr class='prof-$profondeur'>
                    <td class='number'><b>".intval($duree)."</b>&nbsp;ms</td>
                    <td class='name'>".spip_htmlspecialchars($cause)."</td>
                    </tr>\n";
            }
        }
    }
    
    /**
     * Create SubWheel (can be overriden in debug class)
     * @param TextWheelRuleset $rules
     * @return TextWheel
     */
    protected function &createSubWheel(&$rules)
    {
        return new TextWheelDebug($rules);
    }
}
