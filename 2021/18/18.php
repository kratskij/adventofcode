<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->lines();

$p2 = PHP_INT_MIN;
foreach ($input as $k => $one) {
    if ($k == 0) {
        $number = json_decode($one);
    } else {
        $number = add($number, json_decode($one));
    }
    foreach ($input as $two) {
        $p2 = max($p2, magnitude(add(json_decode($one), json_decode($two))));
    }
}
$p1 = magnitude($number);

echo "P1: $p1\nP2: $p2\n";

function add($numberOne, $numberTwo) {
    $number = [$numberOne, $numberTwo];
    $prevJson = false;
    while (json_encode($number) != $prevJson) {
        $prevJson = json_encode($number);
        $ret = true;
        while ($ret !== false) {
            $ret = plode($number);
        }

        splitt($number);
    }

    return $number;
}

function magnitude($s) {
    $magnitude = 0;
    if (is_array($s)) {
        return 3*magnitude($s[0]) + 2*magnitude($s[1]);
    }
    return $s;
}

function splitt(&$s) {
    if (is_array($s)) {
        if (splitt($s[0])) {
            return true;
        } else if (splitt($s[1])) {
            return true;
        }
        return false;
    } else if ($s >= 10){
        $s = [
            floor($s / 2),
            ceil($s / 2),
        ];
        return true;
    }
    return false;
}

function plode(&$s, $l = 0, &$prev = null) {
    if ($l == 4) {
        if (is_array($s)) {
            $ref =& $prev;
            while (is_array($ref)) {
                $ref =& $ref[1];
            }
            $ref += $s[0];
            $addNext = $s[1];
            $s = 0;
            return $addNext;
        }
        return false;
    }
    if (is_array($s[0])) {
        $addNext = plode($s[0], $l+1, $prev);
        if ($addNext !== false) {
            if ($addNext !== true) {
                $ref =& $s[1];
                while (is_array($ref)) {
                    $ref =& $ref[0];
                }
                $ref += $addNext;
            }
            return true;
        }
    }
    if (is_array($s[1])) {
        return plode($s[1], $l+1, $s[0]);
    }

    return false;
}
