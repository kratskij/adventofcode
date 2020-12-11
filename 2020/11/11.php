<?php

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim();

const EMP = "L";
const OCC = "#";
const FLOOR = ".";

$grid = $ir->grid();

echo sprintf(
    "P1: %d\nP2: %d\n",
    countOccupied(stabilize($grid, 4, false)),
    countOccupied(stabilize($grid, 5, true))
);

function countOccupied($grid) {
    return array_sum(array_map(function($row) {
        return substr_count(implode("", $row), "#");
    }, $grid));
}

function stabilize($grid, $tolerance, $lookForever) {
    $dirs = [[0,1], [0,-1], [-1,-1], [-1,0], [-1,1], [1,-1], [1,0], [1,1]];
    $change = true;
    while ($change) {
        $change = false;

        $copy = $grid;
        foreach ($grid as $y => $row) {
            foreach ($row as $x => $val) {
                if ($val === EMP || $val === OCC) {
                    $occCount = 0;
                    foreach ($dirs as $dir) {
                        list($dY, $dX) = $dir;

                        if ($lookForever) {
                            list($origDY, $origDX) = $dir;
                            while (isset($grid[$y+$dY][$x+$dX]) && $grid[$y+$dY][$x+$dX] == FLOOR) {
                                $dY += $origDY;
                                $dX += $origDX;
                            }
                        }
                        if (!isset($grid[$y+$dY][$x+$dX])) {
                            continue;
                        }
                        if ($grid[$y+$dY][$x+$dX] === OCC) {
                            $occCount++;
                        }
                    }

                    if (!$occCount && $val == EMP) {
                        $change = true;
                        $copy[$y][$x] = OCC;
                    } else if ($occCount >= $tolerance && $val == OCC) {
                        $change = true;
                        $copy[$y][$x] = EMP;
                    }
                }
            }
        }

        $grid = $copy;
    }

    return $grid;
}
