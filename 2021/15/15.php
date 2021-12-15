<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$animate = false;

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$grid = $ir->intGrid();

list($targetY, $targetX) = getSize($grid);
$p1 = distance($grid, 0, 0, $targetY, $targetX, $animate);

increaseGrid($grid, 5);

list($targetY, $targetX) = getSize($grid);
$p2 = distance($grid, 0, 0, $targetY, $targetX, $animate);

echo "P1: $p1\nP2: $p2\n";

function getSize(&$grid) {
    $maxY = $maxX = -PHP_INT_MAX;
    foreach ($grid as $y => $row) {
        $maxY = max($maxY, $y);
        $maxX = max($maxX, max(array_keys($row)));
    }

    return [$maxY, $maxX];
}
function increaseGrid(&$grid, $amount) {
    $maxY = $maxX = -PHP_INT_MAX;
    foreach ($grid as $y => $row) {
        $maxY = max($maxY, $y);
        $maxX = max($maxX, max(array_keys($row)));
    }

    $newGrid = [];
    for ($x = 0; $x < $amount; $x++) {
        for ($y = 0; $y < $amount; $y++) {
            foreach ($grid as $y2 => $row) {
                foreach ($row as $x2 => $val) {
                    $newGrid[$y2+($y*($maxY+1))][$x2+($x*($maxX+1))] = ($val + $y + $x);
                    while ($newGrid[$y2+($y*($maxY+1))][$x2+($x*($maxX+1))] > 9) {
                        $newGrid[$y2+($y*($maxY+1))][$x2+($x*($maxX+1))] -= 9;
                    }
                }
            }
        }
    }

    $grid = $newGrid;
}

function distance(&$grid, $fromY, $fromX, $toY, $toX, $animate) {
    $q = [
        [$fromY, $fromX, 0, []]
    ];
    $dGrid = [];

    while ($current = array_shift($q)) {
        list($y, $x, $risk, $tGrid) = $current;

        $risk += $grid[$y][$x];

        if (isset($dGrid[$y][$x]) && $dGrid[$y][$x] <= $risk) {
            continue;
        }
        $dGrid[$y][$x] = $risk;
        $tGrid[$y][$x] = true;

        if ($animate) {
            animate($risk, $tGrid);
        }

        if ($x == $toX && $y == $toY) {
            #animate($risk, $tGrid);
            continue;
        }


        foreach ([[-1,0], [0,-1], [1,0], [0,1]] as $dir) {
            list($dy, $dx) = $dir;
            $idx = ($y+$dy)."_".($x+$dx);
            if (
                !isset($grid[$y+$dy][$x+$dx]) || // off the grid
                (isset($dGrid[$y+$dy][$x+$dx]) && $dGrid[$y+$dy][$x+$dx] <= $risk + $grid[$y+$dy][$x+$dx]) || // already found a shorter path
                (isset($q[$idx]) && $q[$idx][2] <= $risk) // already in queue, from a shorter path
            ) {
                continue;
            }
            $tGridCopy = $tGrid;
            $tGridCopy[$y+$dy][$x+$dx] = true;
            $q[$idx] = [$y+$dy, $x+$dx, $risk, $tGridCopy];
        }
    }

    return $dGrid[$toY][$toX] - $grid[$fromY][$fromX];
}

function animate($risk, $tGrid) {
    static $cache;
    if ($cache === null) {
        $cache = [];
    }
    $l = 100;
    foreach ($cache as $y => $row) {
        foreach ($row as $x => $val) {
            $cache[$y][$x]--;
            if ($cache[$y][$x] <= 0) {
                unset($cache[$y][$x]);
                if (empty($cache[$y])) {
                    unset($cache[$y]);
                }
            }
        }
    }
    $max = 0;
    foreach ($tGrid as $y => $row) {
        foreach ($row as $x => $val) {
            $max = max($max, $cache[$y][$x]);
        }
    }
    foreach ($tGrid as $y => $row) {
        foreach ($row as $x => $val) {
            $cache[$y][$x] = max($l, $max);
        }
    }
    system("clear");
    $out = "$risk\n\n";
    for ($y = 0; $y < max(array_keys($cache)); $y++) {
        if (isset($cache[$y])) {
            for ($x = 0; $x <= max(array_keys($cache[$y])); $x++) {
                if (isset($cache[$y][$x])) {
                    if ($cache[$y][$x] >= $l) {
                        $out .= "█";
                    } else if ($cache[$y][$x] >= $l/2) {
                        $out .= "▒";
                    } else {
                        $out .= "░";
                    }
                } else {
                    $out .= " ";
                }
            }
        }
        $out .= "\n";
    }
    echo "$out\n";
    usleep(10000);
}
