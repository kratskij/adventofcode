<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file);
$input = $ir->regex("(\d+)\,\s(\d+)");

// Cast to int
#$input = array_map("intval", $input);

$val = false;

$grid = [];

$maxX = -INF;
$maxY = -INF;
$minX = INF;
$minY = INF;


$areas = [];
$counts = [];

foreach ($input as $k => $i) {
    list($y, $x) = $i;
    $y = (int)$y;
    $x = (int)$x;

    $maxX = max($x, $maxX);
    $maxY = max($y, $maxY);
    $minX = min($x, $minX);
    $minY = min($y, $minY);

    $grid[$x][$y] = $k;
    $counts[$x][$y] = 1;
}
/*
for ($a = $minX; $a <= $maxX; $a++) {
    for ($b = $minY; $b <= $maxY; $b++) {
        $search = search($grid, [ [$a,$b] ]);
    }
}

function search($grid, $coords) {
    foreach ($coords as $c) {

    }
}*/
/*
$area = $grid;

for ($a = 0; $a < $maxX; $a++) {
    $toClaim = [];
    foreach ($area as $x => $rows) {
        foreach ($rows as $y => $id) {
            if (!isset($area[$x+$a][$y])) {
                if (!isset($toClaim[$x+$a][$y])) {
                    $counts[$x][$y]++;
                    $toClaim[$x+$a][$y] = $id;
                } else {
                    $toClaim[$x+$a][$y] = false;
                }
            }
            if (!isset($area[$x][$y+$a])) {
                if (!isset($toClaim[$x][$y+$a])) {
                    $counts[$x][$y]++;
                    $toClaim[$x][$y+$a] = $id;
                } else {
                    $toClaim[$x][$y+$a] = false;
                }
            }
            if (!isset($area[$x-$a][$y])) {
                if (!isset($toClaim[$x-$a][$y])) {
                    $counts[$x][$y]++;
                    $toClaim[$x-$a][$y] = $id;
                } else {
                    $toClaim[$x-$a][$y] = false;
                }
            }
            if (!isset($area[$x][$y-$a])) {
                if (!isset($toClaim[$x][$y-$a])) {
                    $counts[$x][$y]++;
                    $toClaim[$x][$y-$a] = $id;
                } else {
                    $toClaim[$x][$y-$a] = false;
                }
            }
        }
    }
    foreach ($toClaim as $k => $c) {
        $toClaim[$k] = array_filter($c);
    }
    $area = array_merge($area, $toClaim);
}*/
/*
            if (!isset($area[$x+$a][$y+$a])) {
                $counts[$x][$y]++;
                $area[$x+$a][$y+$a] = $id;
            }
            if (!isset($area[$x-$a][$y-$a])) {
                $counts[$x][$y]++;
                $area[$x-$a][$y-$a] = $id;
            }
            if (!isset($area[$x-$a][$y+$a])) {
                $counts[$x][$y]++;
                $area[$x-$a][$y+$a] = $id;
            }
            if (!isset($area[$x+$a][$y-$a])) {
                $counts[$x][$y]++;
                $area[$x+$a][$y-$a] = $id;
            })*/

$area = $grid;
for ($x = $minX; $x <= $maxX; $x++) {
    for ($y = $minY; $y <= $maxY; $y++) {
        echo "AT $x,$y\n";
        if (isset($grid[$x][$y])) {
            continue;
        }
        $search = [$x . "_" . $y];
        while ($search) {
            $ret = search($search, $grid);
            var_Dump($ret);

            if ($ret[0] == 1) {
                #var_Dump($set ,$x, $y); die();
                $area[$x][$y] = $ret[1][0];
                #echo "$x,$y\n";
                #$change = true;
                #echo strtoupper(chr(65+$set[0]));
                echo "FOUND" .  $ret[1][0]."\n";
                $search = false;
                break;
            }
            if ($ret[0] > 1) {
                $search = false;
                break;
            }
        }
    }
}
#}
#var_dump($area);die();

function search(&$search, &$grid) {
    $count = 0;
    $nextSearch = [];
    while ($search) {
        $next = array_shift($search);
        #var_Dump($next);
        $coords = explode("_", $next);
        #var_Dump($coords[1]);
        if (isset($grid[$coords[0]+1][$coords[1]])) {
            $count++;
            $set = [$grid[$coords[0]+1][$coords[1]], $coords[0]+1, $coords[1]];
        }
        if (isset($grid[$coords[0]][$coords[1]+1])) {
            $count++;
            $set = [$grid[$coords[0]][$coords[1]+1], $coords[0], $coords[1]+1];
        }
        if (isset($grid[$coords[0]-1][$coords[1]])) {
            $count++;
            $set = [$grid[$coords[0]-1][$coords[1]], $coords[0]-1, $coords[1]];
        }
        if (isset($grid[$coords[0]][$coords[1]-1])) {
            $count++;
            $set = [$grid[$coords[0]][$coords[1]-1], $coords[0], $coords[1]-1];
        }

        $nextSearch[] = ($coords[0]+1) . "_" . ($coords[1]);
        $nextSearch[] = ($coords[0]) . "_" . ($coords[1]+1);
        $nextSearch[] = ($coords[0]-1) . "_" . ($coords[1]);
        $nextSearch[] = ($coords[0]) . "_" . ($coords[1]-1);
    }
    $search = array_unique($nextSearch);

    return [$count, $set];

}

for ($x = $minX; $x <= $maxX; $x++) {
    for ($y = $minY; $y <= $maxY; $y++) {
        if (isset($grid[$x][$y])) {
            echo strtoupper(chr(65+$grid[$x][$y]));
        } else if (isset($area[$x][$y])) {
            echo strtolower(chr(65+$area[$x][$y]));
        } else {
            echo ".";
        }
    }
    echo "\n";
}

$counts = [];
$boundaries = [];
for ($x = $minX; $x <= $maxX; $x++) {
    for ($y = $minY; $y <= $maxY; $y++) {
        if (isset($area[$x][$y])) {
            $counts[$area[$x][$y]]++;
            if ($x == $minX || $y == $minY || $x == $maxX || $y == $maxY) {
                $boundaries[$area[$x][$y]] = true;
            }
        }
    }
}

$max = 0;
foreach ($counts as $i => $c) {
    if (!isset($boundaries[$i])) {
        $max = max($max, $c);
    }
}
echo $max;
#var_Dump($counts);die();
