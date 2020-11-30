<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");
require_once(__DIR__ . '/Robot.php');

$ir = new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file);
$code = $ir->trim(true)->explode(",");

$robot = new Robot($code);

$grid = [];
$y = $x = 0;
try {
    while (true) {
        $out = $robot->in(0);

        $chr = chr($out);

        if ($out == 10) {
            $y++;
            $x = 0;
        } else {
            $grid[$y][$x] = $chr;
            $x++;
        }
    }
} catch (End $e) {
    echo "END1\n";
}

$intersections = $realGrid = [];
foreach ($grid as $y => $row) {
    foreach ($row as $x => $val) {
        if (
            $val == '#' &&
            isset($grid[$y-1][$x]) && $grid[$y-1][$x] == '#' &&
            isset($grid[$y+1][$x]) && $grid[$y+1][$x] == '#' &&
            isset($row[$x-1]) && $row[$x-1] == '#' &&
            isset($row[$x+1]) && $row[$x+1] == '#'
        ) {
            $realGrid[$y . "_" . $x] = true;
            $intersections[$y . "_" . $x] = $y * $x;
            echo "0";
        } else if ($val == "^") {
            $robotX = $x;
            $robotY = $y;
            echo $val;
        } else if ($val == "#") {
            $realGrid[$y . "_" . $x] = true;
            echo $val;
        } else {
            echo $val;
        }
    }
    echo "\n";
}
echo "Part 1:" . array_sum($intersections) . "\n";

$robot->wakeup();

$dirs = [
    0 => [0,1],
    1 => [1,0],
    2 => [0,-1],
    3 => [-1,0],
];

$visited = [];
$dir = 3;
$path = [];

$q = [
    [$visited, $robotY, $robotX, $dir, $path]
];
while ($n = array_pop($q)) {
    list($vis, $y, $x, $dir, $p) = $n;
    list($dirY, $dirX) = $dirs[$dir];

    $vis[$y . "_" . $x] = true;

    for ($i = 0; $i < 4; $i++) {
        $dir++;
        if ($dir > 3) {
            $dir = 0;
        }
        list($dirY, $dirX) = $dirs[$dir];

        if (
            isset($realGrid[($y+$dirY) . "_" . ($x+$dirX)]) &&
            (
                !isset($vis[($y+$dirY) . "_" . ($x+$dirX)]) ||
                isset($intersections[($y+$dirY) . "_" . ($x+$dirX)])
            )
        ) {
            $tmpP = $p;
            if ($i == 0) { // right
                $tmpP[] = "R";
            } else if ($i == 1) { //back, not allowed
                #$tmpP[] = 2;
            } else if ($i == 2) { // left
                $tmpP[] = "L";
            } else if ($i == 3) { // ahead
                $nP = array_pop($tmpP);
                if (is_numeric($nP)) {
                    $tmpP[] = $nP + 1;
                } else {
                    $tmpP[] = $nP;
                    $tmpP[] = 2;
                }
            }
            if (count($vis) == count($realGrid)) {
                $path = $tmpP;
                echo "FOUND AN END \nPath: $path\n";
                break 2;
            }
            $q[] = [$vis, $y+$dirY, $x+$dirX, $dir, $tmpP];
            #continue 2;
        }
    }
}

$candidates = [];
$maxLen = 20 / 2; // we need to add commas, so it will be ~twice the length
for ($i = 0; $i < count($path); $i++) {
    for ($j = $i + 3; $j < count($path) && $j <= $i + $maxLen; $j++) {
        $idx = implode(",", array_slice($path, $i, $j-$i));
        if (!isset($candidates[$idx])) {
            $candidates[$idx] = $j-$i; // longer is better
        } else {
            $candidates[$idx]++;
        }
    }
}
$inputs = [];
arsort($candidates);
var_dump(array_slice($candidates, 0, 10));
foreach ($candidates as $a => $aCount) {
    echo "Checking $a\n";
    $t = str_replace($a, "¤", implode(",", $path));
    foreach ($candidates as $b => $bCount) {
        $t = str_replace($b, "¤", $t);
        foreach ($candidates as $c => $cCount) {
            $t = str_replace($c, "¤", $t);
            if (str_replace("¤", "", str_replace(",", "", $t)) == "") {
                $inputs = [ [], $a, $b, $c ];
                $tmpPath = implode(",", $path);
                while (!empty($tmpPath) && count($inputs[0]) < $maxLen) {
                    foreach (["A" => $a, "B" => $b, "C" => $c] as $id => $subPath) {
                        echo "checking if $subPath is in $tmpPath\n";
                        if (substr($tmpPath, 0, strlen($subPath)) == $subPath) {
                            $tmpPath = substr($tmpPath, strlen($subPath) + 1);
                            $inputs[0][] = $id;
                            break;
                        }
                    }
                    echo $tmpPath."\n";
                }
                if (empty($tmpPath)) {
                    $inputs[0] = implode(",", $inputs[0]);
                    echo "DONE!";
                    var_dump($inputs);
                    die();
                    break 3;
                }
            }
        }
    }
}


$inputs = [
    "A,B,A,B,C,A,C,A,C,B",
    "R,12,L,8,L,4,L,4",
    "L,8,R,6,L,6",
    "L,8,L,4,R,12,L,6,L,4",
    "n"
];
$input = 0;
while (true) {
    try {
        $out = $robot->in($input);
        if ($out == 10 && $inputLine = array_shift($inputs)) {
            echo "\nProviding '$inputLine'\n";
            $inputInts = array_map(function($chr) { return ord($chr); }, str_split($inputLine));
            foreach ($inputInts as $i) {
                $robot->in($i);
            }
            $robot->in(10);
        } else {
            echo "" . chr($out) . "";
        }
    } catch (End $e) {
        echo "Part 2: " . $robot->out() . "\n";
        die();
    }
}
