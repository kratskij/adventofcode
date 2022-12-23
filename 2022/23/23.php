<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$grid = $ir->grid(["#" => true, "." => false]);
$grid = array_filter(array_map("array_filter", $grid));

$dirs = [ [-1,0], [1,0], [0,-1], [0,1], ];
$allDirs = [ [0,1], [0,-1], [-1,-1], [-1,0], [-1,1], [1,-1], [1,0], [1,1] ];

foreach ($grid as $y => $rows) {
    foreach ($rows as $x => $val) {
        $grid[$y][$x] = 0;
    }
}

$i = 0;
while(true) {
    $moved = move($grid, $dirs, $allDirs, $i);
    $i++;
    if ($i == 10) {
        $p1 = countEmpty($grid);
    }
    if (!$moved) {
        $p2 = $i;
        break;
    }
}

echo "P1: $p1\nP2: $p2\n";

function move(&$grid, $dirs, $allDirs, $dirIdx) {
    $dirIdx = $dirIdx % 4;
    $sugg = [];
    foreach ($grid as $y => $row) {
        foreach ($row as $x => $moves) {
            $cy = $cx = [];
            $c = 0;
            foreach ($allDirs as $dir) {
                list($dy, $dx) = $dir;
                if (isset($grid[$y+$dy][$x+$dx])) {
                    $cy[$dy] = true;
                    $cx[$dx] = true;
                    $c++;
                }
            }
            if ($c == 0) {
                continue;
            }
            for ($i = 0; $i < 4; $i++) {
                $dir = $dirs[($dirIdx + $i) % 4];
                if ($dir[0] != 0) {
                    if (!isset($cy[$dir[0]])) {
                        $sugg[$y+$dir[0]][$x+$dir[1]][] = [$y, $x];
                        break;
                    }
                } else if ($dir[1] != 0) {
                    if (!isset($cx[$dir[1]])) {
                        $sugg[$y+$dir[0]][$x+$dir[1]][] = [$y, $x];
                        break;
                    }
                }
            }
        }
    }
    $moved = false;
    foreach ($sugg as $toY => $rows) {
        foreach ($rows as $toX => $elves) {
            if (count($elves) == 1) {
                list($y, $x) = $elves[0];

                if (!isset($grid[$toY][$toX])) {
                    $moves = $grid[$y][$x] + 1;
                    $grid[$toY][$toX] = $moves;

                    unset($grid[$y][$x]);
                    if (empty($grid[$y])) {
                        unset($grid[$y]);
                    }
                    $moved = true;
                }
            }
        }
    }
    return $moved;
}

function countEmpty($grid, $print = false) {
    $minY = $minX = PHP_INT_MAX;
    $maxY = $maxX = -PHP_INT_MAX;
    foreach ($grid as $y => $row) {
        $minY = min($minY, $y);
        $maxY = max($maxY, $y);
        $minX = min($minX, min(array_keys($row)));
        $maxX = max($maxX, max(array_keys($row)));
    }
    $out = "";
    $emptySize = 0;
    for ($y = $minY; $y <= $maxY; $y++) {
        for ($x = $minX; $x <= $maxX; $x++) {
            if (!isset($grid[$y][$x])) {
                $emptySize++;
                $out .= "░";
            } else {
                $out .= $grid[$y][$x];
            }
        }
        $out .= "\n";
    }
    if ($print) {
        echo $out;
    }

    return $emptySize;
}
