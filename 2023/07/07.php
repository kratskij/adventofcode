<?php

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->csv(" ");

$p1hands = $p2hands = [];
foreach ($input as $k => $line) {
    [$cards, $bid] = $line;

    $p1hands[getSortKey($cards, false)] = (int)$bid;
    $p2hands[getSortKey($cards, true)] = (int)$bid;
}

$p1 = solve($p1hands);
$p2 = solve($p2hands);

echo "P1: $p1\nP2: $p2\n";

function solve($hands) {
    ksort($hands);
    $totalWinnings = 0;
    $i = 1;
    foreach ($hands as $bid) {
        $totalWinnings += $i++ * $bid;
    }

    return $totalWinnings;
}

function getSortKey($cards, $jIsJoker) {
    $convertionMap = [
        "A" => "E",
        "K" => "D",
        "Q" => "C",
        "J" => $jIsJoker ? "1" : "B",
        "T" => "A"
    ];
    $str = "";
    $frequency = [];
    foreach (str_split($cards) as $c) {
        $hex = $convertionMap[$c] ?? $c;
        $str .= $hex;
        $frequency[$c] = ($frequency[$c] ?? 0) + 1;
    }

    if ($jIsJoker && isset($frequency["J"]) && count($frequency) > 1) {
        $jokerCount = $frequency["J"];
        unset($frequency["J"]);
        $frequency[array_search(max($frequency), $frequency)] += $jokerCount;
    }
    $max = max($frequency);
    
    $type = 0;
    if ($max == 5) {
        $type = 6;
    } else if ($max == 4) {
        $type = 5;
    } else if ($max == 3) {
        $type = (count($frequency) == 2) ? 4 : 3;
    } else if ($max == 2) {
        $type = (count($frequency) == 3) ? 2 : 1;
    }

    return hexdec($type . $str);
}
