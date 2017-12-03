<?php

$test = false;

$file = ($test) ? "test" : "input";
$input = explode("\n", trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file)));
$input = (int)$input[0];

$grid = [];
$x = $y = 0;

$dir = "right";
$p2 = false;

for ($i = 1; $i < $input; $i++) {
    $val = 0;
    if (isset($grid[$x][$y-1])) {
        $val += $grid[$x][$y-1];
    }
    if (isset($grid[$x][$y+1])) {
        $val += $grid[$x][$y+1];
    }
    if (isset($grid[$x-1][$y-1])) {
        $val += $grid[$x-1][$y-1];
    }
    if (isset($grid[$x-1][$y+1])) {
        $val += $grid[$x-1][$y+1];
    }
    if (isset($grid[$x+1][$y-1])) {
        $val += $grid[$x+1][$y-1];
    }
    if (isset($grid[$x+1][$y+1])) {
        $val += $grid[$x+1][$y+1];
    }
    if (isset($grid[$x-1][$y])) {
        $val += $grid[$x-1][$y];
    }
    if (isset($grid[$x+1][$y])) {
        $val += $grid[$x+1][$y];
    }

    if ($x == 0 && $y == 0) {
        $val = 1;
    }

    $grid[$x][$y] = $val;
    if ($val >= $input && $p2 === false) {
        $p2 = $val;
    }

    switch($dir) {
        case "right":
            $x++;
            if (!isset($grid[$x][$y-1])) {
                $dir = "up";
            }
            break;
        case "up":
            $y--;
            if (!isset($grid[$x-1][$y])) {
                $dir = "left";
            }
            break;
        case "left":
            $x--;
            if (!isset($grid[$x][$y+1])) {
                $dir = "down";
            }
            break;
        case "down":
            $y++;
            if (!isset($grid[$x+1][$y])) {
                $dir = "right";
            }
            break;
    }
}

echo "Part 1: " . (abs($x) + abs($y)) . "\n";
echo "Part 2: " . $p2 . "\n";
#echo $sum;
#echo $outString;
