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
                    if ($grid[$nY][$nX] == KEY || $grid[$nY][$nX] == ENTRANCE) {
                        $paths[$ky][$kx][$nY][$nX][] = [$c, $nU];
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

$i = 0;
foreach ($paths as $p) {
    foreach ($p as $r) {
        foreach ($r as $q) {
            $i += count($q);
        }
    }
}
echo "Found $i paths\n";

echo shortest($paths, $y, $x, $res, $keyCount + 1);
die();

function shortest(&$paths, $y, $x, $res, $keyCount, &$min = PHP_INT_MAX, $c = 0) {

    static $cs;
    if ($cs === null) {
        $cs = [];
    }
    $cs[$c]++;
    if ($c == 18) {
        var_Dump($cs);
    }
    $endPaths = [PHP_INT_MAX];
    foreach ($paths[$y][$x] as $ty => $r) {
        foreach ($r as $tx => $path) {
            foreach ($path as $info) {
                $idx = "$ty.$tx";
                if (isset($res[$idx])) {
                    continue;
                }
                list($d, $required) = $info;

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
                if ($sum >= $min) {
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
            }
        }
    }
    return min($endPaths);
}
die();

$dir = 0;


$unlockCount = 0;
foreach ($keys as $d) { $unlockCount += count($d); }
echo "looking for $unlockCount keys\n";
$q = [
    [$y, $x, [], [], 0, 0, ["0_0" => true] ]
];

$c = 0;
while ($n = array_shift($q)) {
    list($y, $x, $myKeys, $unlocked, $steps, $dir, $vis) = $n;

    for ($i = 0; $i < 4; $i++) {
        if ($dir == 3) {
            $dir = 0;
        } else {
            $dir++;
        }
        list($dirY, $dirX) = $dirs[$dir];
        if (
            isset($grid[$y+$dirY]) &&
            isset($grid[$y+$dirY][$x+$dirX])
            && $grid[$y+$dirY][$x+$dirX] != WALL
        ) {
            $tmpVis = $vis;
            if (!isset($tmpVis[($y+$dirY) . "_" . ($x+$dirX)])) {
                printGrid($grid, $y+$dirY, $x+$dirX, $tmpVis);
            }
            $tmpVis[($y+$dirY) . "_" . ($x+$dirX)] = true;

            $type = $grid[$y+$dirY][$x+$dirX];
            switch ($type) {
                case WALL;
                    break;
                case OPEN:
                case ENTRANCE:
                case DOOR;
                    $q[] = [$y+$dirY, $x+$dirX, $myKeys, $unlocked, $steps + 1, $dir, $tmpVis];
                    break;
                case KEY:
                    $tmpMyKeys = $myKeys;
                    #if (
                        #isset($doorKeys[$keys[$y+$dirY][$x+$dirX]]) &&
                    #    !isset($unlocked[$keys[$y+$dirY][$x+$dirX]])) {
                        $tmpMyKeys[$keys[$y+$dirY][$x+$dirX]] = true;
                    #}
                    echo count($tmpMyKeys) . "::" . $unlockCount."\n";
                    if (count($tmpMyKeys) == $unlockCount) {
                        echo "FOUND IT! after " . ($steps + 1) . " steps\n";
                        die();
                    }
                    $q[] = [$y+$dirY, $x+$dirX, $tmpMyKeys, $unlocked, $steps + 1, $dir, $tmpVis];
                    break;
                case DOOR:
                    $tmpUnlocked = $unlocked;
                    $tmpMyKeys = $myKeys;
                    if (isset($tmpMyKeys[$doors[$y+$dirY][$x+$dirX]])) {
                        echo "hei\n";
                        $tmpUnlocked[$doors[$y+$dirY][$x+$dirX]] = true;
                        unset($tmpMyKeys[$doors[$y+$dirY][$x+$dirX]]);
                    }
                    var_Dump("---", $tmpUnlocked, $tmpMyKeys, $doors[$y+$dirY][$x+$dirX]);
                    $q[] = [$y+$dirY, $x+$dirX, $tmpMyKeys, $tmpUnlocked, $steps + 1, $dir, $tmpVis];
                    break;
            }
        }
    }
    #printGrid($grid, $y, $x);
    #usleep(100000);
    if (!isset($max) || count($myKeys) > $max) {
        $max = count($myKeys);
        echo "new max: $max\n";

    }



    //lets rearrange the queue
    #if ((++$c % 1000) == 0) {
        usort($q, function($a, $b) {
            #if ($a[3] == $b[3]) {
                return count($b[6]) - count($a[6]);
            #}
            #return count($a[3]) - count($b[3]);
        });
        #$q = array_slice($q, 0, 1000);
        array_reverse($q);
        #echo count($q[0][6])."\n";
        #printGrid($grid, $q[0][0], $q[0][1], $q[0][6]);
    #}
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
