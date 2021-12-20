<?php

require_once(__DIR__."/../inputReader.php");

$file = $argv[1] ?? "input";
$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
list($enhancement, $gridData) = explode("\n\n", $ir->raw());
$flipInfinite = ($enhancement[0] == "#" && $enhancement[strlen($enhancement) - 1] == ".");

$grid = [];
foreach (explode("\n", $gridData) as $y => $line) {
    foreach (str_split($line) as $x => $c) {
        $grid[$y][$x] = ($c == "#");
    }
}

$minY = $minX = PHP_INT_MAX;
$maxY = $maxX = -PHP_INT_MAX;
foreach ($grid as $y => $row) {
    $minY = min($minY, $y);
    $maxY = max($maxY, $y);
    $minX = min($minX, min(array_keys($row)));
    $maxX = max($maxX, max(array_keys($row)));
}

$i = 0;
while(++$i && --$minY && --$minX && ++$maxY && ++$maxX) {
    $copy = $grid;
    for ($y = $minY; $y <= $maxY; $y++) {
        for ($x = $minX; $x <= $maxX; $x++) {
            $pos = 0;
            for ($dy = -1; $dy <= 1; $dy++) {
                for ($dx = -1; $dx <= 1; $dx++) {
                    $pos = ($pos << 1) | intval($copy[$y+$dy][$x+$dx] ?? ($flipInfinite && ($i % 2) == 0));
                }
            }

            $grid[$y][$x] = ($enhancement[$pos] == "#");
        }
    }
    if ($i == 2) {
        $p1 = array_sum(array_map("count", array_map("array_filter", $grid)));
    }
    if ($i == 50) {
        $p2 = array_sum(array_map("count", array_map("array_filter", $grid)));
        break;
    }
}

echo "P1: $p1\nP2: $p2\n";
