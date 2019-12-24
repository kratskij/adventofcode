<?php

ini_set('memory_limit','8192M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file);

const WALL = '#';
const OPEN = '.';
const NONE = " ";
const IN = "A";
const OUT = "Z";

$origGrid = $ir->grid([]);

$from = $to = false;

$dirs = [
    [0,1],
    [0,-1],
    [1,0],
    [-1,0],
];

$portals = $tmpPortals = $grid = $portalNames = [];
foreach ($origGrid as $y => $row) {
    foreach ($row as $x => $val) {
        if ($portalName = isPortal($origGrid, $y, $x, $dirs)) {
            $portalNames["$y.$x"] = $portalName;
            if ($portalName == "AA") {
                $from = "$y.$x";
                $portals["$y.$x"] = "$y.$x";
            } else if ($portalName == "ZZ") {
                $to = "$y.$x";
                $portals["$y.$x"] = "$y.$x";
            } else {
                if (isset($tmpPortals[$portalName])) {
                    $portals["$y.$x"] = $tmpPortals[$portalName];
                    $portals[$tmpPortals[$portalName]] = "$y.$x";
                } else {
                    $tmpPortals[$portalName] = "$y.$x";
                }
            }
        }
        if ($val == ".") {
            $grid[$y][$x] = true;
        }
    }
}
$distances = portalDistances($grid, $dirs, $portals);

echo "Part 1: " . shortestPath($from, $to, $portals, $distances, false, $portalNames) . "\n";
#4989
echo "Part 2: " . shortestPath($from, $to, $portals, $distances, true, $portalNames) . "\n";

function isOuter($idx, &$portals) {
    static $outerYs;
    static $outerXs;
    if ($outerYs === null) {
        $ys = array_map(
            function($p) {
                return explode(".", $p)[0];
            },
            array_keys($portals)
        );
        $xs = array_map(
            function($p) {
                return explode(".", $p)[1];
            },
            array_keys($portals)
        );
        $outerYs = [ min($ys), max($ys) ];
        $outerXs = [ min($xs), max($xs) ];
    }

    return (in_array(explode(".", $idx)[0], $outerYs) || in_array(explode(".", $idx)[1], $outerXs));
}

function shortestPath($from, $to, $portals, $distances, $recursiveMode = false, $portalNames = []) {

    $v = [];
    $q = [
        [$from, 0, $v, 0, []]
    ];
    $min = PHP_INT_MAX;

    while ($n = array_shift($q)) {
        list($idx, $steps, $v, $level) = $n;

        /*echo $level. ": ";
        foreach ($v as $w => $null) {;
            if (!isset($portalNames[implode(".", array_slice(explode(".", $w), 0, 2))])) {
                echo "?,";
            } else {
                echo $portalNames[implode(".", array_slice(explode(".", $w), 0, 2))].",";
            }
        }
        echo "\n";*/
        if ($idx == $to) {
            if (!$recursiveMode || ($recursiveMode && $level == -1) && $steps - 1 < $min) {
                $min = $steps - 1;
            }
            continue;
        }
        if ($idx == $from && $level != 0) {
            continue;
        }
        if ($steps >= $min) {
            continue;
        }
        if ($level > 100) {
            continue;
        }
        foreach ($distances[$idx] as $toIdx => $d) {
            if (
                $level == 0 &&
                isOuter($toIdx, $portals) &&
                $toIdx != $from && $toIdx != $to
            ) { // outer layer; not entrance/exit
                continue;
            }
            if ($level > 1 && ($toIdx == $from || $toIdx == $to)) {
                continue;
            }

            $tmpLevel = $level;
            if ($recursiveMode) {
                if (isOuter($toIdx, $portals)) {
                    $tmpLevel--;
                } else {
                    $tmpLevel++;
                }
            }

            $tmpV = $v;
            $tmpV[$portals[$toIdx].".".$tmpLevel] = true;
            $q[] = [$portals[$toIdx], $steps + $d +1 , $tmpV, $tmpLevel];
        }
    }

    return $min;
}

function portalDistances($grid, $dirs, $portals) {
    $distances = [];
    foreach ($portals as $from => $null) {
        $p = explode(".", $from);
        $q = [
            [$p[0], $p[1], 0, []]
        ];
        while ($n = array_shift($q)) {
            list($y, $x, $steps, $v) = $n;
            $v["$y.$x"] = true;
            foreach ($dirs as $dir) {
                $ny = $y + $dir[0];
                $nx = $x + $dir[1];
                if (isset($v["$ny.$nx"])) {
                    continue;
                }
                if (isset($grid[$ny][$nx])) {
                    if (isset($portals["$ny.$nx"])) {
                        $distances[$from]["$ny.$nx"] = $steps + 1;
                    } else {
                        $q[] = [$ny, $nx, $steps+1, $v];
                    }
                }
            }
        }
    }

    return $distances;

}

function isPortal(&$grid, $y, $x, &$dirs) {
    $ret = [];
    if ($grid[$y][$x] !== OPEN) {
        return false;
    }
    foreach ($dirs as $dir) {
        if (
            isset($grid[$y+$dir[0]][$x+$dir[1]]) &&
            !in_array($grid[$y+$dir[0]][$x+$dir[1]], [WALL, NONE, OPEN]) &&
            isset($grid[$y+$dir[0]*2][$x+$dir[1]*2]) &&
            !in_array($grid[$y+$dir[0]*2][$x+$dir[1]*2], [WALL, NONE, OPEN])
        ) {
            if (array_sum($dir) > 0) {
                return $grid[$y+$dir[0]][$x+$dir[1]] . $grid[$y+$dir[0]*2][$x+$dir[1]*2];
            } else {
                return $grid[$y+$dir[0]*2][$x+$dir[1]*2] . $grid[$y+$dir[0]][$x+$dir[1]];
            }
        }
    }
    return false;
}

function printGrid($grid, $ty, $tx, $vis = []) {
    $minY = $minX = PHP_INT_MAX;
    $maxY = $maxX = -PHP_INT_MAX;
    foreach ($grid as $y => $row) {
        $minY = min($minY, $y);
        $maxY = max($maxY, $y);
        $minX = min($minX, min(array_keys($row)));
        $maxX = max($maxX, max(array_keys($row)));
    }

    $out = "\n";
    for ($y = $minY; $y <= $maxY; $y++) {
        for ($x = $minX; $x <= $maxX; $x++) {
            if (!isset($grid[$y][$x])) {
                echo "░";
            } else {
                if (isset($vis[$y.".".$x])) {
                    echo "█";
                } else {
                    echo " ";
                }
            }
        }
        echo "\n";
    }
}
