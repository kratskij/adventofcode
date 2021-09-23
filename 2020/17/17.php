<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);


$grid = $ir->grid(["#" => true, "." => false]);

$cube = [];
$l = count($grid);

function reduce($structure) {
    $min = PHP_INT_MAX;
    $max = 0;
    foreach ($structure as $s) {
        if (is_array($s)) {
            $min = min($min, min(array_keys($s)));
            $max = max($ma, max(array_keys($s)));
        }
    }
    for ($i = $min - 1; $i = $max + 1; $i++) {
        $slice = [];
        foreach ($j = -1; $j <= 1; $j++) {
            if (isset($structure[$i+$j])) {
                $slice[$i+$j] = $structure[$i+$j];
            }
        }
        reduce($slice);
    }


    $ref = &$structure;
    while (($pos = next($positions)) !== false) {
        $ref = $structure[$pos];
    }
    foreach ()
    $position = array_shift($args);
    if (!isset($structure[$position])) {
        $structure[$position] = false;
    }

        reduce($structure, $structure[$position], [$position])
    }

}

function solve($dimensions, $structure) {
    $mins = [];
    $maxes = [];
    for ($i = $dimensions; $i >= 1; $i--) {
        $newRefs = [];
        $mins[$i] = PHP_INT_MAX;
        $maxes[$i] = 0;
        foreach ($refs as $k => $ref) {
            $mins[$i] = min($mins[$i], $k);
            $maxes[$i] = max($maxes[$i], $k);
            if (is_array($ref)) {
                foreach ($ref as $j => $val) {
                    $newRefs[$j] = $val;
                }
            }
        }
        $refs = $newRefs;
    }

    $allDirs = getDirections($dim);
    $ref = $structure;
    $dim = $dimensions;
    while ($min = $mins[$dim]) {
        $max = $maxes[$dim];
        for ($i = $min - 1; $i <= $max + 1; $i++) {
            $allDirsCopy = $allDirs;
            while ($dirs = array_pop($allDirsCopy)) {

            }
        }
        $dim--;
    }


            foreach ($dirs as $dir) {
                $ref = &$ref[$i+$di];
                foreach ($dir as $d) {
                    $ref = &$structure[$d];
                }
            }
        }
    }

    var_dump($mins, $maxes);die();


}
$p1 = solve(3, [$grid]);
$p2 = solve(4, [[$grid]]);

$nwmin = 0;
$nwmax = 0;
$nzmin = 0;
$nzmax = 0;
$nymin = 0;
$nymax = count($grid) - 1;
$nxmin = 0;
$nxmax = count($grid) - 1;

for ($i = 0; $i < 6; $i++) {
    $copy = $cube;

    $wmin = $nwmin;
    $wmax = $nwmax;

    $zmin = $nzmin;
    $zmax = $nzmax;
    $ymin = $nymin;
    $ymax = $nymax;
    $xmin = $nxmin;
    $xmax = $nxmax;
    echo "$i: $wmin,$wmax,$zmin,$zmax,$ymin,$ymax,$xmin,$xmax\n";

    for ($w = $wmin-1; $w <= $wmax+1; $w++) {
        for ($z = $zmin-1; $z <= $zmax+1; $z++) {
            echo "|$z,$w|\n";
            for ($y = $ymin-1; $y <= $ymax+1; $y++) {
                for ($x = $xmin-1; $x <= $xmax+1; $x++) {
                    $c = 0;
                    foreach ($wAllDirs as $dir) {
                        list($zd,$xd,$yd,$wd) = $dir;

                        if (isset($cube[$z+$zd][$w+$wd][$y+$yd][$x+$xd]) && $cube[$z+$zd][$w+$wd][$y+$yd][$x+$xd]) {
                            $c++;
                        }
                    }

                    if (isset($cube[$z][$w][$y][$x]) && $cube[$z][$w][$y][$x] && $c != 2 && $c != 3) {
                        $copy[$z][$w][$y][$x] = false;
                    } else if ((!isset($cube[$z][$w][$y][$x]) || !$cube[$z][$w][$y][$x]) && $c == 3) {
                        $nwmin = min($nwmin, $w);
                        $nwmax = max($nwmax, $w);

                        $nzmin = min($nzmin, $z);
                        $nzmax = max($nzmax, $z);
                        $nymin = min($nymin, $y);
                        $nymax = max($nymax, $y);
                        $nxmin = min($nxmin, $x);
                        $nxmax = max($nxmax, $x);
                        $copy[$z][$w][$y][$x] = true;
                    }
                    echo (isset($copy[$z][$w][$y][$x]) && $copy[$z][$w][$y][$x]) ? "#" : ".";
                }
                echo "\n";
            }
        }
    }

    $cube = $copy;
}
#var_dump($cube);
$sum = 0;
foreach ($cube as $z) {
    foreach ($z as $w) {
        foreach ($w as $y) {
            foreach ($y as $x) {
                if ($x) {
                    $sum++;
                }
            }
        }
    }
}

echo $sum."\n";

function getDirections($dimensions) {
    static $dirs;
    if ($dirs === null) {
        $dirs = [];
    }
    if (!isset($dirs[$dimensions])) {
        $dirs = [
            1 => [ [-1], [1] ]
        ];
        $i = 2;
        while ($i <= $dimensions) {
            $allLowerDirs = $dirs[$i-1];
            $allLowerDirs[] = array_fill(0, $i-1, 0);
            foreach ($allLowerDirs as $dir) {

                foreach ([-1, 0, 1] as $newDir) {
                    $nDir = $dir;
                    $nDir[] = $newDir;
                    if (array_filter($nDir)) {
                        $dirs[$i][] = $nDir;
                    }
                }
            }
            $i++;
        }
    }
    return $dirs[$dimensions];
}
