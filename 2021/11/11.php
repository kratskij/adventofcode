<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$grid = $ir->grid();

$octopi = 0;
foreach ($grid as $y => $row) {
    foreach ($row as $x => $val) {
        $grid[$y][$x] = (int)$val;
        $octopi++;
    }
}

$p1 = $p2 = $i = 0;
while(true) {
    $i++;
    $flashCount = 0;

    $flashGrid = [];
    foreach ($grid as $y => $row) {
        foreach ($row as $x => $val) {
            $grid[$y][$x] += 1;
            if ($grid[$y][$x] > 9) {
                flash($grid, $flashGrid, $y, $x);
            }
        }
    }

    foreach ($flashGrid as $y => $row) {
        foreach ($row as $x => $val) {
            $flashCount++;
            $grid[$y][$x] = 0;
        }
    }

    if ($i <= 100) {
        $p1 += $flashCount;
    }

    if ($flashCount == $octopi) {
        $p2 = $i;
        break;
    }
}

echo "P1: $p1\nP2: $p2\n";

function flash(&$grid, &$flashGrid, $y, $x) {
    if (!isset($flashGrid[$y][$x])) {
        $flashGrid[$y][$x] = true;
        foreach ([-1,0,1] as $dy) {
            foreach ([-1,0,1] as $dx) {
                if (($dy === 0 && $dx === 0) || !isset($grid[$x+$dx][$y+$dy])) {
                    continue;
                }
                $grid[$y+$dy][$x+$dx] += 1;
                if ($grid[$y+$dy][$x+$dx] > 9) {
                    flash($grid, $flashGrid, $y+$dy, $x+$dx);
                }
            }
        }
    }
}
