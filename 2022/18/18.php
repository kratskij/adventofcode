<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->csv(",");

$shape = [];
$minX = $minY = $minZ = PHP_INT_MAX;
$maxX = $maxY = $maxZ = -PHP_INT_MAX;
foreach ($input as $k => $line) {
    list($x, $y, $z) = array_map("intval", $line);
    $shape[$x][$y][$z] = true;
    $minX = min($minX, $x);
    $maxX = max($maxX, $x);
    $minY = min($minY, $y);
    $maxY = max($maxY, $y);
    $minZ = min($minZ, $z);
    $maxZ = max($maxZ, $z);
}

$dirs = [
    [0,0,1],
    [0,1,0],
    [1,0,0],
    [0,0,-1],
    [0,-1,0],
    [-1,0,0],
];

// let's define the edges of the external universe,
// by selecting a point we know is outside of the shape, and then BFS our way around the shape
$universe = [];
$queue = [
    [$minX-1, $minY-1, $minZ-1] // definitely outside our shape
];
while ($state = array_shift($queue)) {
    list($x,$y,$z) = $state;
    $universe[$x][$y][$z] = true;
    foreach ($dirs as $dir) {
        list($dx,$dy,$dz) = $dir;
        if (!isset($shape[$x+$dx][$y+$dy][$z+$dz]) && !isset($universe[$x+$dx][$y+$dy][$z+$dz])) {
            if (
                ($x+$dx >= $minX-1 && $x+$dx <= $maxX+1) &&
                ($y+$dy >= $minY-1 && $y+$dy <= $maxY+1) &&
                ($z+$dz >= $minZ-1 && $z+$dz <= $maxZ+1)
            ) {
                $idx = ($x+$dx)."_".($y+$dy)."_".($z+$dz);
                if (!isset($queue[$idx])) {
                    $queue[$idx] = [$x+$dx, $y+$dy, $z+$dz];
                }
            }
        }
    }
}

$res = exposed($shape, $universe, $dirs);
$p1 = $res["all"];
$p2 = $res["external"];

echo "P1: $p1\nP2: $p2\n";

function exposed($shape, $universe, $dirs) {
    $return = [
        "all" => 0,
        "external" => 0
    ];
    foreach ($shape as $x => $ys) {
        foreach ($ys as $y => $zs) {
            foreach ($zs as $z => $null) {
                foreach ($dirs as $dir) {
                    list($dx, $dy, $dz) = $dir;
                    if (!isset($shape[$x+$dx][$y+$dy][$z+$dz])) {
                        $isExternal = isset($universe[$x+$dx][$y+$dy][$z+$dz]);
                        $return["all"] += 1;
                        if ($isExternal) {
                            $return["external"] += 1;
                        }
                    }
                }
            }
        }
    }

    return $return;
}
