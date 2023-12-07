<?php

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->csv(" ");

$p1 = $p2 = 0;

$p1hands = $p2hands = [];
foreach ($input as $k => $line) {
    [$cards, $bid] = $line;

    $p1hands[getSortKey($cards, false)] = (int)$bid;
    $p2hands[getSortKey($cards, true)] = (int)$bid;
}
ksort($p1hands);
ksort($p2hands);

$i = 1;
foreach ($p1hands as $bid) {
    $p1 += $i++ * $bid;
}
$i = 1;
foreach ($p2hands as $bid) {
    $p2 += $i++ * $bid;
}

echo "P1: $p1\nP2: $p2\n";

function getSortKey($cards, $jIsJoker) {
    $convertionMap = [
        "A" => "E",
        "K" => "D",
        "Q" => "C",
        "J" => $jIsJoker ? "1" : "B",
        "T" => "A"
    ];
    $str = "";
    $counts = [];
    foreach (str_split($cards) as $c) {
        $hex = $convertionMap[$c] ?? $c;
        $str .= $hex;
        $counts[$c] = ($counts[$c] ?? 0) + 1;
    }

    if ($jIsJoker && isset($counts["J"])) {
        $jokerCount = $counts["J"];
        unset($counts["J"]);
        if (empty($counts)) {
            $counts["A"] = 5;
        } else {
            $counts[array_search(max($counts), $counts)] += $jokerCount;
        }
    }
    $max = max($counts);
    
    $type = "";
    if ($max == 5) {
        $type = "6";
    } else if ($max == 4) {
        $type = "5";
    } else if ($max == 3) {
        if (count($counts) == 2) {
            $type = "4";
        } else {
            $type = "3";
        }
    } else if ($max == 2) {
        if (count($counts) == 3) {
            $type = "2";
        } else {
            $type = "1";
        }
    }

    return hexdec($type . $str);
}
