<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file);

$grid = $ir->trim(true)->grid([]);

const BUG = "#";
const NONE = ".";
const INWARD = "?";


$dirs = [
    [1,0],
    [0,-1],
    [-1,0],
    [0,1],
];

echo "Part 1: " . biodiv(getFirstRepeat($grid, $dirs)) . "\n";
echo "Part 2: " . countRecursiveBugs($grid, $dirs, 200) . "\n";

function getFirstRepeat($grid, $dirs) {
    $hashes = [];
    while (true) {
        $tmpg = $grid;
        foreach ($grid as $y => $row) {
            foreach ($row as $x => $val) {
                $n = neighbours($grid, $y, $x, $dirs);

                if ($val == BUG) {
                    if ($n != 1) {
                        $tmpg[$y][$x] = NONE;
                    }
                } else if ($val == NONE) {
                    if ($n == 1 || $n == 2) {

                        $tmpg[$y][$x] = BUG;
                    }
                }
            }
        }
        $grid = $tmpg;
        $hash = md5(implode(",", array_map(function($row) { return implode(",", $row); }, $grid)));
        if (isset($hashes[$hash])) {
            return $grid;
        }
        $hashes[$hash] = true;
    }
}

function countRecursiveBugs($grid, $dirs, $stopAt) {
    $grid[2][2] = "?";

    $levelCoords = [
        "1.2" => [ 1 => [ [0,0],[0,1],[0,2],[0,3],[0,4] ] ],
        "2.1" => [ 1 => [ [0,0],[1,0],[2,0],[3,0],[4,0] ] ],
        "2.3" => [ 1 => [ [0,4],[1,4],[2,4],[3,4],[4,4] ] ],
        "3.2" => [ 1 => [ [4,0],[4,1],[4,2],[4,3],[4,4] ] ],

        "0.0" => [ -1 => [ [1,2],[2,1] ] ],
        "0.4" => [ -1 => [ [1,2],[2,3] ] ],
        "4.0" => [ -1 => [ [2,1],[3,2] ] ],
        "4.4" => [ -1 => [ [2,3],[3,2] ] ],

        "0.1" => [ -1 => [ [1,2] ] ],
        "0.2" => [ -1 => [ [1,2] ] ],
        "0.3" => [ -1 => [ [1,2] ] ],

        "4.1" => [ -1 => [ [3,2] ] ],
        "4.2" => [ -1 => [ [3,2] ] ],
        "4.3" => [ -1 => [ [3,2] ] ],

        "1.0" => [ -1 => [ [2,1] ] ],
        "2.0" => [ -1 => [ [2,1] ] ],
        "3.0" => [ -1 => [ [2,1] ] ],

        "1.4" => [ -1 => [ [2,3] ] ],
        "2.4" => [ -1 => [ [2,3] ] ],
        "3.4" => [ -1 => [ [2,3] ] ],
    ];


    $emptyGrid = [
        [ ".", ".", ".", ".", "." ],
        [ ".", ".", ".", ".", "." ],
        [ ".", ".", "?", ".", "." ],
        [ ".", ".", ".", ".", "." ],
        [ ".", ".", ".", ".", "." ],
    ];

    $count = 0;
    $grids = [0 => $grid];
    for ($i = 1; $i < $stopAt; $i++) {
        $grids[$i] = $emptyGrid;
        $grids[-$i] = $emptyGrid;
    }
    ksort($grids);

    for ($i = 0; $i < $stopAt; $i++) {
        $tmpg = $grids;
        foreach ($grids as $z => $grid) {
            foreach ($grid as $y => $row) {
                foreach ($row as $x => $val) {
                    if ($val == "?") {
                        continue;
                    }
                    $n = recNeighbours($grids, $z, $y, $x, $dirs, $levelCoords, $emptyGrid);

                    if ($val == BUG) {
                        if ($n != 1) {
                            $tmpg[$z][$y][$x] = NONE;
                        }
                    } else if ($val == NONE) {
                        if ($n == 1 || $n == 2) {

                            $tmpg[$z][$y][$x] = BUG;
                        }
                    }
                }
            }
        }
        $grids = $tmpg;
        $count++;
    }
    #echo implode("\n", $prints);
    return countBugs($grids);
}

function neighbours(&$grid, $y ,$x, $dirs) {
    $c = 0;
    foreach ($dirs as $d) {
        list($yd,$xd) = $d;
        if (isset($grid[$y+$yd][$x+$xd]) && $grid[$y+$yd][$x+$xd] == BUG) {
            $c++;
        }
    }
    return $c;
}

function recNeighbours(&$grids, $z, $y ,$x, $dirs, $levelCoords) {
    $c = 0;
    $done = [];
    foreach ($dirs as $d) {
        list($yd,$xd) = $d;
        if (!isset($grids[$z][$y+$yd][$x+$xd]) || $grids[$z][$y+$yd][$x+$xd] == "?") {
            if (isset($done["$y.$z"])) {
                continue;
            }
            $done["$y.$z"] = true;;
            foreach ($levelCoords["$y.$x"] as $zd => $coords) {
                foreach ($coords as $coord) {
                    if (!isset($grids[$z+$zd])) {
                        continue;
                    }

                    if ($grids[$z+$zd][$coord[0]][$coord[1]] == BUG) {
                        $c++;
                    }
                }
            }
        } else {
            if ($grids[$z][$y+$yd][$x+$xd] == BUG) {
                $c++;
            }
        }
    }
    return $c;
}

function countBugs($grids) {
    $c = 0;
    foreach ($grids as $grid) {
        foreach ($grid as $row) {
            foreach ($row as $val) {
                if ($val == BUG) {
                    $c++;
                }
            }
        }
    }
    return $c;
}


function biodiv($grid) {
    $sum = $c = 0;
    foreach ($grid as $y => $row) {
        foreach ($row as $x => $val) {
            if ($val == BUG) {
                $sum += 2**$c;
            }
            $c++;
        }
    }
    return $sum;
}
