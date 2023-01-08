<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");
require_once(__DIR__."/../Util.php");
require_once(__DIR__."/../../Toolbox.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim();
$grid = $ir->grid();

$p1 = $p2 =  "";

const WALL = "#";
const GROUND = ".";
const UP = "^";
const DOWN = "v";
const LEFT = "<";
const RIGHT = ">";
const ME = "E";

$dirs = [
    RIGHT => [0,1],
    DOWN => [1,0],
    LEFT => [0,-1],
    UP => [-1,0]
];

$height = count($grid)-2;
$width = count($grid[0])-2;
$repeatsAfter = $width*$height;

foreach ($grid[0] as $x => $val) {
    if ($val != WALL) {
        $grid[0][$x] = ME;
        $startX = $x-1;
        $startY = -1;
        break;
    }
}
foreach ($grid[count($grid)-1] as $x => $val) {
    if ($val != WALL) {
        $endX = $x-1;
        $endY = count($grid)-1;
        $inside[$endY][$endX] = true;
    }
}
$blissards = $inside = [];
foreach ($grid as $y => $row) {
    foreach ($row as $x => $val) {
        if ($val != WALL) {
            $inside[$y-1][$x-1] = true;
        }
        if (isset($dirs[$val])) {
            $blissards[$y-1][$x-1] = $val;
        }
    }
}

$blissardConf = [];
function getBlissards(&$blissards, $dirs, $i, $height, $width) {
    $blissardConf = [];
    foreach ($blissards as $y => $row) {
        foreach ($row as $x => $dir) {
            #var_dump($dirs, $dir, $dirs[$dir]);
            list($dy, $dx) = $dirs[$dir];
            $newY = (($y + $dy*$i)+($height*$i)) % $height;
            $newX = (($x + $dx*$i)+($width*$i)) % $width;
            #echo "$newY,$newX\n";
            if (!isset($blissardConf[$newY][$newX])) {
                $blissardConf[$newY][$newX] = [];
            }
            $blissardConf[$newY][$newX][] = $dir;
        }
    }
    return $blissardConf;
}

$minutes = 0;
for ($i = 0; $i < 3; $i++) {
    $fromY = (($i % 2) == 0) ? $startY : $endY;
    $fromX = (($i % 2) == 0) ? $startX : $endX;
    $toY = (($i % 2) == 0) ? $endY : $startY;
    $toX = (($i % 2) == 0) ? $endX : $startX;

    $minutes += run($fromY, $fromX, $toY, $toX, $minutes, $dirs, $height, $width, $repeatsAfter, $blissards, $inside);
    seen(0, true);
    if ($i == 1) {
        $p1 = $minutes;
    }
    $p2 = $minutes;
}

echo "P1: $p1\nP2: $p2\n";

function run($fromY, $fromX, $toY, $toX, $startMinutes, $dirs, $height, $width, $repeatsAfter, $blissards, $inside) {
    $q = [
        [ $startMinutes, $fromY, $fromX ]
    ];
    while ($state = array_shift($q)) {
        list($minutes, $meY, $meX) = $state;
        if ($meY == $toY && $meX == $toX) {
            return $minutes - $startMinutes - 1;
        }

        $cp = getBlissards($blissards, $dirs, $minutes % $repeatsAfter, $height, $width);
        $cp[$meY][$meX] = ME;

        $minutes++;
        $bs = getBlissards($blissards, $dirs, $minutes % $repeatsAfter, $height, $width);
        foreach ($dirs as $dir) {
            list($dy, $dx) = $dir;
            if (
                !isset($bs[$meY+$dy][$meX+$dx]) &&
                (isset($inside[$meY+$dy][$meX+$dx]) || ($meY+$dy == $toY && $meX+$dx == $toX)) &&
                !seen([$minutes % $repeatsAfter, $meY+$dy, $meX+$dx])
            ) {
                $q[] = [$minutes, $meY+$dy, $meX+$dx];
            }
        }
        if (
            !isset($bs[$meY][$meX]) &&
            !seen([$minutes % $repeatsAfter, $meY, $meX])
        ) {
            $q[] = [$minutes, $meY, $meX];
        }
    }
}


function seen($grid, $reset = false) {
    static $state;
    if ($state === null || $reset) {
        $state = [];
    }

    $idx = md5(json_encode($grid));
    if (!isset($state[$idx])) {
        $state[$idx] = true;
        return false;
    }

    return true;
}
