<?php

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->lines();

$p1 = $p2 = 0;
foreach ($input as $k => $line) {
    [$game, $colors] = explode(": ", $line);
    $gameId = explode(" ", $game)[1];
    $colors = explode("; ", $colors);
    $max = [];
    foreach ($colors as $color) {
        foreach (explode(", ", $color) as $c) {
            $parts = explode(" ", $c);
            $max[$parts[1]] = max($max[$parts[1]] ?? 0, $parts[0]);
        }
    }

    $p2 += $max["red"] * $max["green"] * $max["blue"];

    if ($max["red"] <= 12 && $max["green"] <= 13 && $max["blue"] <= 14) {
        $p1 += $gameId;
    }
}

echo "P1: $p1\nP2: $p2\n";
