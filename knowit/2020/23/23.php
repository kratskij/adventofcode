<?php

if (!function_exists('mb_str_split')) {
    function mb_str_split($string, $split_length = 1)
    {
        if ($split_length == 1) {
            return preg_split("//u", $string, -1, PREG_SPLIT_NO_EMPTY);
        } elseif ($split_length > 1) {
            $return_value = [];
            $string_length = mb_strlen($string, "UTF-8");
            for ($i = 0; $i < $string_length; $i += $split_length) {
                $return_value[] = mb_substr($string, $i, $split_length, "UTF-8");
            }
            return $return_value;
        } else {
            return false;
        }
    }
}

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->regex("(.*):\s(.*)");

if (false && $test) {
    define("BASE", [
        "snø" => 5,"hygge" => 7,"grøt" => 10,"pepperkake" => 6,"nisse" => 2
    ]);
} else {
    define("BASE", [
        "pepperkake" => 4,"grøt" => 3,"nellik" => 3,"snø" => 6,"pipe" => 10,"nisse" => 2,"reinsdyr" => 9,
        "gave" => 7,"tre" => 1,"hygge" => 1,"stjerne" => 4,"nordpolen" => 6,"appelsin" => 10,
        "kalender" => 9,"ferie" => 5,"pynt" => 5,"slede" => 6,"lykt" => 6
    ]);
}

$rapperSum = [];
foreach ($input as $k => $line) {
    list($rapper, $phrase) = $line;
    if (!isset($rapperSum[$rapper])) {
        $rapperSum[$rapper] = 0;
    }

    $prev = false;
    $rep = 1;
    $sum = 0;
    foreach (explode(" ", $phrase) as $word) {
        $sum += calcSum($word, $prev, $rep);
        $prev = $word;
    }

    $rapperSum[$rapper] += $sum;
}
echo array_search(max($rapperSum), $rapperSum) . "," . max($rapperSum) . "\n";

# CORRECT: 3316


function vowelBonus($word, $prev) {
    $sum = 0;
    if ($prev) {
        $sum += max(countVowels($word) - countVowels($prev), 0);
    }
    return $sum;
}

function juleDobling($word) {
    return (baseWord($word) != $word) ? 2 : 1;
}

function repDiv($word, $prev, &$rep) {
    if (baseWord($word) == baseWord($prev)) {
        $rep++;
    } else {
        $rep = 1;
    }
}

function baseWord($word) {
    if (substr($word, 0, 4) === "jule") {
        return substr($word, 4);
    }
    return $word;
}

function baseValue($word) {
    return BASE[baseWord($word)];
}
function calcSum($word, $prev, &$rep) {
    $baseValue = baseValue($word);
    $vowelBonus = vowelBonus($word, $prev);
    $juleDobling = juleDobling($word);
    $repDiv = repDiv($word, $prev, $rep);

    $wordValue = floor(($baseValue + $vowelBonus * $juleDobling) / $rep);
    #echo "\t$wordValue\t$word (" . baseWord($word) . "): \t($baseValue + $vowelBonus * $juleDobling) / $rep\n";

    return $wordValue;

}

function countVowels($word) {
    static $vowels;
    if ($vowels === null) {
        $vowels = array_flip(mb_str_split("aeiouyæøå"));
    }
    $sum = 0;
    foreach (mb_str_split($word) as $c) {
        if (isset($vowels[$c])) {
            $sum++;
        }
    }
    return $sum;
}
