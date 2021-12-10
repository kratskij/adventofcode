<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$grid = $ir->grid([]);
foreach ($grid as $y => $row) {
    foreach ($row as $x => $val) {
        $grid[$y][$x] = (int)$val;
    }
}

$basins = [];

$p1 = 0;
foreach ($grid as $y => $row) {
    foreach ($row as $x => $val) {
        if (
            (!isset($grid[$y-1][$x]) || $grid[$y-1][$x] > $val) &&
            (!isset($grid[$y+1][$x]) || $grid[$y+1][$x] > $val) &&
            (!isset($grid[$y][$x-1]) || $grid[$y][$x-1] > $val) &&
            (!isset($grid[$y][$x+1]) || $grid[$y][$x+1] > $val)
        ) {
            $p1 += 1 + $val;

            $v = [];
            $q = [ [$y,$x] ];
            while ($c = array_pop($q)) {
                list($y2,$x2) = $c;
                if (isset($v["$y2,$x2"]) || !isset($grid[$y2][$x2]) || $grid[$y2][$x2] == 9) {
                    continue;
                }
                $v["$y2,$x2"] = true;
                $q[] = [$y2-1,$x2];
                $q[] = [$y2+1,$x2];
                $q[] = [$y2,$x2-1];
                $q[] = [$y2,$x2+1];
            }
            ksort($v);
            $basins[md5(json_encode($v))] = $v;
        }
    }
}

$sizes = array_map("count", $basins);
sort($sizes);

$p2 = array_product(array_slice($sizes, -3));

echo "P1: $p1\nP2: $p2\n";
