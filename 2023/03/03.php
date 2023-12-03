<?php

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);

$grid = $ir->grid();

$allDirs = [ [0,1], [0,-1], [-1,-1], [-1,0], [-1,1], [1,-1], [1,0], [1,1] ];

$p1 = $p2 = 0;
$gearNeighbours = [];
for ($y = 0; $y < count($grid); $y++) {
    for ($x = 0; $x < count($grid[0]); $x++) {
        $hasNeighbour = false;
        $rev = 0;
        $num = getNumberAt($grid, $y, $x, $rev);

        if ($num) {
            foreach ($allDirs as $dirs) {
                [$yd, $xd] = $dirs;
                if ($rev == 0) {
                    for ($xd2 = 0; $xd2 < strlen($num); $xd2++) {
                        if (isSymbol($grid[$y+$yd][$x+$xd+$xd2] ?? null)) {
                            $hasNeighbour = true;
                            break;
                        }
                    }
                }

                if (($grid[$y+$yd][$x+$xd] ?? null) == "*") {
                    $gearNeighbours[$y+$yd][$x+$xd][$y."_".($x-$rev-1)] = $num;
                }
            }
        }

        if ($hasNeighbour) {
            $p1 += $num;
        }
    }
}

foreach ($gearNeighbours as $row) {
    foreach ($row as $vals) {
        if (count($vals) == 2) {
            $p2 += array_product($vals);
        }
    }
}

function isSymbol($val) {
    return ($val != null && $val != "." && !is_numeric($val));
}

function getNumberAt($grid, $y, $x, &$rev) {
    if (!is_numeric($grid[$y][$x])) {
        return null;
    }

    while ($x > 0 && is_numeric($grid[$y][$x])) {
        $x--;
        $rev++;
    }
    if (!is_numeric($grid[$y][$x])) {
        $x++;
        $rev--;
    }
    $num = "";
    while (is_numeric($grid[$y][$x] ?? null)) {
        $num .= $grid[$y][$x];
        $x++;
    }

    return (int)$num;
}

echo "P1: $p1\nP2: $p2\n";
