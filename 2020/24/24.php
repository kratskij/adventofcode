<?php

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$grid = createGrid($ir->lines());

$p1 = count($grid);
for ($day = 1; $day <= 100; $day++) {
    play($grid);
}
$p2 = count($grid);

echo "P1: $p1\nP2: $p2\n";

function createGrid($input) {
    $dirs = ["e", "se", "sw", "w", "nw", "ne"];
    $grid = [];
    foreach ($input as $k => $line) {
        $z = $y = $x = 0;
        while ($line) {
            foreach ($dirs as $dir) {
                if (substr($line, 0, strlen($dir)) == $dir) {
                    $theDir = substr($line, 0, strlen($dir));
                    $line = substr($line, strlen($dir));

                    switch ($theDir) {
                        case "e":
                            $x++; $y--; break;
                        case "se":
                            $y--; $z++; break;
                        case "sw":
                            $z++; $x--; break;
                        case "w":
                            $x--; $y++; break;
                        case "nw":
                            $y++; $z--; break;
                        case "ne":
                            $z--; $x++; break;
                    }
                }
            }
        }
        if (isset($grid["$z,$x,$y"])) {
            unset($grid["$z,$x,$y"]);
        } else {
            $grid["$z,$x,$y"] = true;
        }
    }
    return $grid;
}

function play(&$grid) {
    static $dirs;
    if ($dirs === null) {
        $dirs = [ [1, -1, 0], [1, 0, -1], [0, 1, -1], [-1, 1, 0], [-1, 0, 1], [0, -1, 1] ];
    }

    $check = $grid;
    foreach ($grid as $idx => $none) {
        list($z, $x, $y) = explode(",", $idx);
        foreach ($dirs as $dir) {
            list($zd, $xd, $yd) = $dir;
            $check[($z+$zd) . "," . ($x+$xd) . "," . ($y+$yd)] = true;
        }
    }

    $gc = $grid;
    foreach ($check as $idx => $none) {
        list($z, $x, $y) = explode(",", $idx);
        $blackTileCount = 0;
        foreach ($dirs as $dir) {
            list($zd, $xd, $yd) = $dir;
            if (isset($grid[($z+$zd) . "," . ($x+$xd) . "," . ($y+$yd)])) {
                $blackTileCount++;
            }
        }

        if (isset($grid[$idx]) && ($blackTileCount == 0 || $blackTileCount > 2)) {
            unset($gc[$idx]);
        } else if (!isset($grid[$idx]) && $blackTileCount == 2) {
            $gc[$idx] = true;
        }
    }
    $grid = $gc;
}
