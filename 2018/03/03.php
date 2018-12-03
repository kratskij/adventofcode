<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once("../inputReader.php");

$ir = new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file);
$input = $ir->lines();

$area = [];
foreach ($input as $k => $i) {
    list($id, $at, $pos, $size) = explode(" ", $i);
    $id = substr($id, 1);
    list($leftOffset, $topOffset) = explode(",", $pos);
    $topOffset = substr($topOffset, 0, -1);
    list($width, $height) = explode("x", $size);

    for ($x = $leftOffset; $x < $leftOffset + $width; $x++) {
        for ($y = $topOffset; $y < $topOffset + $height; $y++) {
            $area[$x][$y][] = $id;
        }
    }
}

$sum = 0;
$ids = [];
foreach ($area as $x => $a) {
    foreach ($a as $y => $b) {
        if (count($b) > 1) {
            $sum++;
        }
        foreach ($b as $id) {
            $ids[$id] = max(count($b), $ids[$id]);
        }
    }
}
echo "Part 1: $sum\n";

foreach ($ids as $id => $e) {
    if ($e == 1) {
        echo "Part 2: $id\n";
        exit;
    }
}
