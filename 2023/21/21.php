<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");
#require_once __DIR__."/../Util.php";

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim();
$p1 = $p2 = 0;

$dirs = [ [0,1], [1,0], [0,-1], [-1,0] ];
$grid = $ir->grid(['#' => false, "." => true]);
$q = [];

foreach ($grid as $y => $row) {
    foreach ($row as $x => $val) {
        if ($val === "S") {
            $startAt = [$y,$x,0,0,0];
            $grid[$y][$x] = true;
        }
    }
}

$q = [$startAt];

$v = [];
$m = ($test) ? 6 : 64;

$m = ($test) ? 100 : 26501365;

#$m = 12;
$cache = [];
$goal = [];
$v = [];
$h = count($grid);
$w = count($grid[0]);
while ($c = array_shift($q)) {
    [$y, $x, $steps, $yy, $xx] = $c;
    $idx = $y."_".$x;
    if (isset($v[$y][$x][$steps][$yy . "_" . $xx])) {
        continue;
    }
    if ($steps > $m) {
        continue;
    }

    $v[$y][$x][$steps][$yy . "_" . $xx] = ($v[$y][$x][$steps][$yy . "_" . $xx] ?? 0) + 1;
    $steps++;
    #echo $steps."\n";
    foreach ($dirs as $dir) {
        [$yd,$xd] = $dir;
        $newY = $y+$yd;
        $newX = $x+$xd;
        $newYY = $yy;
        $newXX = $xx;
        if (!isset($grid[$newY])) {
            if ($newY < 0) {
                $newY += $h;
                $newYY = $yy-1;;
            } else {
                $newY = 0;
                $newYY = $yy+1;
            }
        }
        if (!isset($grid[$newY][$newX])) {
            if ($newX < 0) {
                $newX += $w;
                $newXX = $xx-1;
            } else {
                $newX = 0;
                $newXX = $xx+1;
            }
        }
        if ($grid[$newY][$newX] === true) {
            $qIdx = $newY . "_" . $newX . "_" . $steps . "_" . $newYY . "_" . $newXX;
            $q[$qIdx] = [$newY, $newX, $steps, $newYY, $newXX];
        }
    }
}
$p1 = 0;
foreach ($grid as $y => $row) {
    foreach ($row as $x => $isGarden) {
        if (isset($v[$y][$x])) {
            $found = false;
            foreach ($v[$y][$x] as $steps => $val) {
                if ($steps == $m && $isGarden) {
                    $p2+=count($val);
                    echo count($val);
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                echo ".";
            }
        } else if ($isGarden) {
            echo ".";
        } else {
            echo "#";
        }
    }
    echo "\n";
}

#$p1 = count($goal);
#var_dump($goal);

echo "P1: $p1\nP2: $p2\n";

#51?, 47, 33? 99, 243
