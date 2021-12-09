<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->lines();
$p1 = $p2 = 0;

$digitRules = [
    0 => ["6 chars", "4 has 3 overlaps", "1 has 2 overlaps"],
    1 => ["2 chars"],
    2 => ["5 chars", "9 has 4 overlaps"],
    3 => ["5 chars", "7 has 3 overlaps"],
    4 => ["4 chars"],
    5 => ["5 chars", "6 has 5 overlaps"],
    6 => ["6 chars", "7 has 2 overlaps"],
    7 => ["3 chars"],
    8 => ["7 chars"],
    9 => ["6 chars", "4 has 4 overlaps"],
];

foreach ($input as $k => $line) {
    list($signalPattern, $outputSignals) = explode(" | ", $line);
    $resolvedSignals = [];
    $unresolvedSignals = explode(" ", $signalPattern);
    while (($currentWord = array_shift($unresolvedSignals)) && count($resolvedSignals) != 10) {
        $solved = false;
        foreach ($digitRules as $digit => $rules) {
            if (isset($resolvedSignals[(int)$digit])) {
                continue;
            }

            $allRules = true;
            while ($rule = array_shift($rules)) {
                # overlap rule
                preg_match("/^(\d+)\shas\s(\d+)\soverlaps$/", $rule, $matches);
                if ($matches) {
                    list($null, $source, $overlaps) = array_map("intval", $matches);
                    if (!isset($resolvedSignals[$source]) || count(array_intersect(str_split($currentWord), str_split($resolvedSignals[$source]))) != $overlaps) {
                        $allRules = false;
                        break;
                    }
                }

                # char count rule
                preg_match("/^(\d+)\schars$/", $rule, $matches);
                if ($matches && strlen($currentWord) != $matches[1]) {
                    continue 2;
                }
            }
            if ($allRules) {
                $solved = true;
                $resolvedSignals[(int)$digit] = sortChars($currentWord);
                break;
            }
        }
        if (!$solved) {
            $unresolvedSignals[] = $currentWord;
        }
    }

    $digit = "";
    foreach (explode(" ", $outputSignals) as $w) {
        $d = array_search(sortChars($w), $resolvedSignals);
        $digit .= $d;

        if (in_array($d, [1,4,7,8])) {
            $p1++;
        }
    }
    $p2 += (int)$digit;
}
echo "P1: $p1\nP2: $p2\n";

function sortChars($str) {
    $sortedWord = str_split($str);
    sort($sortedWord);
    return implode("", $sortedWord);
}
