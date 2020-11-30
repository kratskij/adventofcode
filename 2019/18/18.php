<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file);
$grid = $ir->trim()->grid([]);

const WALL = '#';
const OPEN = '.';
const ENTRANCE = '@';
const KEY = "-";
const DOOR = "_";

$y = $x = 0;

$keys = $doors = [];
$keyCount = 0;
$keyPositions = [];
foreach ($grid as $tmpY => $row) {
    foreach ($row as $tmpX => $v) {
        switch ($v) {
            case ENTRANCE:
                $x = $tmpX;
                $y = $tmpY;
                break;
            case WALL:
                break;
            case OPEN:
                break;
            default:
                if ($v === strtolower($v)) {
                    $keys[$tmpY][$tmpX] = strtoupper($v);
                    $grid[$tmpY][$tmpX] = KEY;
                    $keyCount++;
                    $keyPositions[strtoupper($v)] = [$tmpY, $tmpX];
                } else {
                    $doors[$tmpY][$tmpX] = $v;
                    $grid[$tmpY][$tmpX] = DOOR;
                    $doorKeys[$v] = true;
                }
        }
    }
}

$dirs = [
    0 => [0,1],
    1 => [1,0],
    2 => [0,-1],
    3 => [-1,0],
];

#guessed 5210

$paths = [];
$keys[$y][$x] = "@";
foreach ($keys as $ky => $row) {
    foreach ($row as $kx => $name) {
        $q = [
            [$ky, $kx, [], 1, []]
        ];
        while ($nQ = array_shift($q)) {
            list($ty, $tx, $v, $c, $u) = $nQ;
            foreach ($dirs as $dir) {
                $nY = $ty+$dir[0];
                $nX = $tx+$dir[1];
                $nV = $v;
                $nU = $u;
                if (
                    !isset($nV[$nY][$nX]) &&
                    !($nY == $ky && $nX == $kx) &&
                    $grid[$nY][$nX] != WALL
                ) {
                    $nV[$nY][$nX] = true;
                    if ($grid[$nY][$nX] == KEY) {
                        $paths["$ky.$kx"][$c]["$nY.$nX"] = $nU;
                        krsort($paths["$ky.$kx"]);

                        #$nV = [$nY => [$nX => true]];
                        #$nV = $nU;
                    } else if ($grid[$nY][$nX] == DOOR) {
                        $keyPos = $keyPositions[$doors[$nY][$nX]];
                        $nU[$keyPos[0]][$keyPos[1]] = true;
                    }
                    $q[] = [$nY, $nX, $nV, $c+1, $nU];
                }
            }
        }
    }
}

$res = ["$y.$x" => 0];
var_Dump("countpath", count($paths));

#echo "Found $i paths\n";

echo shortest($paths, $y, $x, $res, $keyCount + 1);
die();

function shortest(&$paths, $y, $x, $res, $keyCount, &$min = PHP_INT_MAX, $c = 0) {

/*
    static $cs;
    if ($cs === null) {
        $cs = [];
    }
    @$cs[$c]++;
    if ($c == 12) {
        var_Dump($cs);
    }
    static $bests;
    if ($bests === null) {
        $bests = [];
    }
    if (!isset($bests[$c + 1])) {
        $bests[$c + 1] = PHP_INT_MAX;
    }
*/

    $endPaths = [PHP_INT_MAX];
    foreach ($paths["$y.$x"] as $d => $infos) {
        foreach ($infos as $idx => $required) {
            list($ty,$tx) = explode(".", $idx);
        #foreach ($r as $tx => $info) {
            #foreach ($path as $info) {
                #$idx = "$ty.$tx";
                if (isset($res[$idx])) {
                    continue;
                }
                #list($d, $required) = $info;

                foreach ($required as $ry => $row) {
                    foreach ($row as $rx => $none) {
                        if (!isset($res["$ry.$rx"])) {
                            continue 3;
                        }
                    }
                }
                $tmpRes = $res;
                $tmpRes[$idx] = $d;

                $sum = array_sum($tmpRes);

                #$bests[$c + 1] = min($sum, $bests[$c + 1]);

                #if ($sum > $bests[$c + 1] * 1.01) {
                    #echo "skipping at " . ($c+1) . " because $sum is bigger than {$bests[$c+1]} * 1.1\n";
                    #continue;
                #}
                if ($sum >= $min) {
                    #echo "sum is too high ($sum)\n";
                    continue;
                }
                if (count($tmpRes) == $keyCount) {
                    if ($sum < $min) {
                        $min = $sum;
                        echo "NEW MIN!";
                        #var_dump($tmpRes);
                        echo $sum."\n";
                    }
                    return $min;
                }
                $endPaths[] = shortest($paths, $ty, $tx, $tmpRes, $keyCount, $min, $c+1);
            #}
        }
    }
    return min($endPaths);
}
die();

function shuffle_assoc(&$array) {
     $keys = array_keys($array);

     shuffle($keys);
     shuffle($keys);

     foreach($keys as $key) {
         $new[$key] = $array[$key];
     }

     $array = $new;

     return true;
 }

function printGrid($grid, $ty, $tx, $vis = []) {
    foreach ($grid as $y => $row) {
        foreach ($row as $x => $val) {
            if ($y == $ty && $x == $tx) {
                echo "█";
            } else {
                if (isset($vis[$y."_".$x])) {
                    echo " ";
                } else {
                    echo $val;
                }
            }
        }
        echo "\n";
    }
}
