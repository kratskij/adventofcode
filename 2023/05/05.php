<?php

ini_set('memory_limit','12048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");
#require_once __DIR__."/../Util.php";

[$seeds, $p2seeds, $maps, $revMaps] = parseInput($file);

$p1 = PHP_INT_MAX;
$source = "seed";
$destination = "location";
foreach ($seeds as $curNumber) {
    $curType = $source;
    while ($curType != $destination) {
        [$curType, $curNumber] = getTypeAndPos($maps, $curType, $curNumber);
    }
    $p1 = min($p1, $curNumber);
}

$destination = "seed";
$source = "location";
$i = 0;
while (true) {
    $i++;
    $curNumber = $i;
    $curType = $source;
    while ($curType != $destination) {
        [$curType, $curNumber] = getTypeAndPos($revMaps, $curType, $curNumber);
    }

    foreach ($p2seeds as $seed) {
        [$start, $end] = $seed;
        if ($curNumber >= $start && $curNumber <= $end) {
            $p2 = $i;
            break 2;
        }
    }
}

echo "P1: $p1\nP2: $p2\n";

function getTypeAndPos($maps, $type, $number) {
    $destination = $maps[$type]["destination"];
    foreach ($maps[$type]["entries"] as $t) {
        if ($t["sourceStart"] <= $number && $number <= $t["sourceEnd"]) {
            return [$destination, $t["destStart"] + ($number - $t["sourceStart"])];
        }
    }

    return [$destination, $number];
}

function parseInput($file) {
    $ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
    $input = $ir->lines();
    $input = array_filter($input);
    $parts = explode(" ", array_shift($input));
    array_shift($parts);
    $seeds = array_map("intval", $parts);

    $seedsCopy = $seeds;
    $p2seeds = [];
    while ($seedsCopy) {
        [$start, $length] = [array_shift($seedsCopy), array_shift($seedsCopy)];
        $p2seeds[] = [$start, $start + $length - 1];
    }

    $maps = $revMaps = [];

    foreach ($input as $k => $line) {
        if (strpos($line, ":") > 0) {
            $parts = explode("-", $line);
            $source = $parts[0];
            $parts = explode(" map:", $parts[2]);
            $destination = $parts[0];
            continue;
        }

        [$destStart, $sourceStart, $length] = array_map("intval", explode(" ", $line));
        $maps[$source]["destination"] = $destination;
        $maps[$source]["source"] = $source;
        $maps[$source]["entries"][] = [
            "destStart" => (int)($destStart),
            "destEnd" => (int)($destStart + $length - 1),
            "sourceStart" => (int)($sourceStart),
            "sourceEnd" => (int)($sourceStart + $length - 1),
            "length" => (int)($length),
        ];
        $revMaps[$destination]["destination"] = $source;
        $revMaps[$destination]["source"] = $destination;
        $revMaps[$destination]["entries"][] = [
            "sourceStart" => (int)($destStart),
            "sourceEnd" => (int)($destStart + $length - 1),
            "destStart" => (int)($sourceStart),
            "destEnd" => (int)($sourceStart + $length - 1),
            "length" => (int)($length),
        ];
    }

    return [$seeds, $p2seeds, $maps, $revMaps];
}
