<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");
require_once __DIR__."/../Util.php";

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim();

$input = $ir->regex("^([A-Z]+)\s(\d+)\s\(\#([0-9a-f]{5})([0-9])\)$");
#var_dump($input);

$p1 = $p2 = 0;

$dirs = [
    "R" => [0, 1],
    "L" => [0, -1],
    "U" => [-1, 0],
    "D" => [1, 0],
];
$p2dirs = [
    [0, 1],
    [1, 0],
    [0, -1],
    [-1, 0],
];
$y = 0; $x = 0;

$lines = $horLines = $vertLines = [];
foreach ($input as $k => $line) {
    [$dir, $len, $p2len, $p2dir] = $line;
    [$dy,$dx] = $dirs[$dir];
    #$lines[] = [$y, $x, $y+$dy*$len, $x+$dx*$len];

    #$len = hexdec($p2len);
    # [$dy,$dx] = $p2dirs[$p2dir];

    $fromY = min($y, $y+$dy*$len);
    $toY = max($y, $y+$dy*$len);
    $fromX = min($x, $x+$dx*$len);
    $toX = max($x, $x+$dx*$len);

    $lines[] = [$fromY, $fromX, $toY, $toX];
    $y += $dy * $len;
    $x += $dx * $len;
}
usort($lines, function($a, $b) {
    return $a[1] - $b[1];
});


$minY = min(array_column($lines, 0));
$maxY = max(array_column($lines, 0));

$minX = min(array_column($lines, 1));
$maxX = max(array_column($lines, 1));
echo "$minY-$maxY, x: $minX-$maxX\n";

$p2 = 0;
$prevX = 0;
for ($y = $minY; $y <= $maxY; $y++) {
    $count = 0;
    $x = $minX;
    $first = true;
    foreach ($lines as $l) {
        [$fromY, $fromX, $toY, $toX] = $l;
        if ($fromY == $toY && $fromY == $y) { // horizontal line
            #$count += ($toX - $fromX) + 1;
            #echo "$y: adding horline " . (($toX - $fromX) + 1) . "\n";
        } else { // vertical line
            if ($fromY <= $y && $y <= $toY) {
                if (!$first) {
                    $count += $fromX - $x + 1;
                    echo "$y: adding vertline " . ($fromX - $x + 1) . "\n";
                }
                $first = false;
                $x = $fromX;
            }
        }
    }
    $p2 += $count;
}

function isInside($y, $x, $lines) {
    $inside = false;
    foreach ($lines as $line) {
        [$fromY, $fromX, $toY, $toX] = $line;
        if ($fromY <= $y && $y <= $toY && $fromX <= $x && $x <= $toX) {
            $inside = !$inside;
        }
    }
    return $inside;
}

#$p1 = array_sum(array_map("count", $grid));

echo "P1: $p1\nP2: $p2\n";
