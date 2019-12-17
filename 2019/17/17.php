<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");
require_once(__DIR__ . '/Robot.php');

$ir = new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file);
$code = $ir->trim(true)->explode(",");

$robot = new Robot($code);

$grid = [];
$y = $x = 0;
try {
    while (true) {
        $out = $robot->in(0);

        $chr = chr($out);

        if ($out == 10) {
            $y++;
            $x = 0;
        } else {
            $grid[$y][$x] = $chr;
            $x++;
        }
    }
} catch (End $e) {
    echo "END1\n";
}

$intersections = $realGrid = [];
foreach ($grid as $y => $row) {
    foreach ($row as $x => $val) {
        if (
            $val == '#' &&
            isset($grid[$y-1][$x]) && $grid[$y-1][$x] == '#' &&
            isset($grid[$y+1][$x]) && $grid[$y+1][$x] == '#' &&
            isset($row[$x-1]) && $row[$x-1] == '#' &&
            isset($row[$x+1]) && $row[$x+1] == '#'
        ) {
            $realGrid[$y . "_" . $x] = true;
            $intersections[$y . "_" . $x] = $y * $x;
            echo "0";
        } else if ($val == "^") {
            $robotX = $x;
            $robotY = $y;
            echo $val;
        } else if ($val == "#") {
            $realGrid[$y . "_" . $x] = true;
            echo $val;
        } else {
            echo $val;
        }
    }
    echo "\n";
}
echo "Part 1:" . array_sum($intersections) . "\n";

$robot->wakeup();
$in = [0, $]
while (true) {
    try {
        $out = $robot->in(0);
        if ($out == 10) {

        } else {
            echo "" . chr($out) . "";

        }
    } catch (End $e) {
        die();
    }
}
$dirs = [
    0 => [0,1],
    1 => [1,0],
    2 => [0,-1],
    3 => [-1,0],
];

$visited = [];
$dir = 3;
$path = [];

$q = [
    [$realGrid, $robotY, $robotX, $dir, $path]
];
while ($n = array_shift($q)) {
    list($g, $y, $x, $dir, $p) = $n;
    list($dirY, $dirX) = $dirs[$dir];

    unset($g[$y . "_" . $x]);
    $moved = false;

    if (
        isset($intersections[$y . "_" . $x]) || // at an intersection
        !isset($g[($y+$dirY) . "_" . ($x+$dirX)]) // at starting position, or corner
    ) {
        for ($i = 0; $i < 3; $i++) {
            $dir++;
            if ($dir > 3) {
                $dir = 0;
            }
            list($dirY, $dirX) = $dirs[$dir];
            if (isset($g[($y+$dirY) . "_" . ($x+$dirX)])) {
                if ($i == 0) { // right
                    $p[] = "R";
                } else if ($i == 1) { //ahead
                    $nP = array_pop($p);
                    $nP += 1;
                    $p[] = $nP;
                } else if ($i == 2) { //left
                    $p[] = "L";
                }
                $q[] = [$g, $y+$dirY, $x+$dirX, $dir, $p];
                array_pop($p);
                $moved = true;
            }
        }
    } else {
        if (isset($g[($y+$dirY) . "_" . ($x+$dirX)])) {
            $nP = array_pop($p);
            if (is_numeric($nP)) {
                $nP++;
                $p[] = $nP;
            } else {
                $p[] = $nP;
                $p[] = 1;
            }
            $q[] = [$g, $y+$dirY, $x+$dirX, $dir, $p];
            $moved = true;
        }
    }
    if (empty($g)) {
        echo "FOUND AN END \nPath:$p\n";
    }
    if (!$moved) {
        //dead end; been here before
        #var_Dump($p);
        #die();
    }
}
