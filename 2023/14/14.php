<?php

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$grid = $ir->grid();

$p1 = tilt($grid, [ [-1, 0] ], 1);
$p2 = tilt($grid, [ [-1, 0], [0, -1], [1, 0], [0, 1] ], 1000000000);

echo "P1: $p1\nP2: $p2\n";

function tilt($grid, $dirs, $stopAt) {
    $cache = [];
    $skipped = false;
    for ($i = 0; $i < $stopAt; $i++) {
        if (!$skipped) {
            $idx = md5(json_encode([$grid]));
            if (isset($cache[$idx])) {
                $cycleLength = $i - $cache[$idx];
                $i = $cache[$idx] + $cycleLength * (floor($stopAt / $cycleLength));
                while ($i > $stopAt) {
                    $i -= $cycleLength;
                }
                $skipped = true;
            } else {
                $cache[$idx] = $i;
            }
        }

        foreach ($dirs as $dir) {
            [$dy, $dx] = $dir;
            $yRange = ($dy > 0) ? array_reverse(array_keys($grid)) : array_keys($grid);
            $xRange = ($dx > 0) ? array_reverse(array_keys($grid[0])) : array_keys($grid[0]);
            foreach ($yRange as $y) {
                foreach ($xRange as $x) {
                    $val = $grid[$y][$x];
                    if ($val == "O") {
                        $y2 = $y;
                        $x2 = $x;
                        while(isset($grid[$y2+$dy][$x2+$dx]) && $grid[$y2+$dy][$x2+$dx] == ".") {
                            $y2 += $dy;
                            $x2 += $dx;
                        }

                        if ($y2 != $y || $x2 != $x) {
                            $grid[$y2][$x2] = "O";
                            $grid[$y][$x] = ".";
                        }
                    }
                }
            }
        }
    }

    $value = 0;
    foreach ($grid as $y => $row) {
        foreach ($row as $x => $val) {
            if ($val == "O") {
                $value += count($grid) - $y;
            }
        }
    }

    return $value;
}
