<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->explode("\n\n");

$p1 = $p2 = 0;

$validate = [
    "byr" => function($v) { return $v >= 1920 && $v <= 2002; },
    "iyr" => function($v) { return $v >= 2010 && $v <= 2020; },
    "eyr" => function($v) { return $v >= 2020 && $v <= 2030; },
    "hgt" => function($v) {
        $unit = substr($v, -2);
        $units = (int)substr($v, 0, -2);
        return ($unit == "cm" && $units >= 150 && $units <= 193) || ($unit == "in" && $units >= 59 && $units <= 76);
    },
    "hcl" => function($v) { return preg_match("/^\#[0-9a-f]{6}$/", $v) ; },
    "ecl" => function($v) { return preg_match("/^(amb|blu|brn|gry|grn|hzl|oth)$/", $v); },
    "pid" => function($v) { return preg_match("/^\d{9}$/", $v); },
];

foreach ($input as $k => $passport) {
    preg_match_all("/[^\s]+/", $passport, $fields);
    $doc = [];
    foreach ($fields[0] as $field) {
        list($id, $value) = explode(":", $field);
        if (isset($validate[$id])) {
            $doc[$id] = $validate[$id]($value);
        }
    }
    if (count($validate) == count($doc)) {
        $p1++;
        if (count(array_filter($doc)) == count($doc)) {
            $p2++;
        }
    }
}

echo "P1: $p1\nP2: $p2\n";
