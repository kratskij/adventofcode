<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");
#require_once __DIR__."/../Util.php";

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim();
$input = $ir->regex("^(\d+),(\d+),(\d+)\~(\d+),(\d+),(\d+)$");

$cubes = [];
foreach ($input as $k => $line) {
    $line = array_map("intval", $line);
    [$x1, $x2] = [ min($line[0], $line[3]), max($line[0], $line[3]) ];
    [$y1, $y2] = [ min($line[1], $line[4]), max($line[1], $line[4]) ];
    [$z1, $z2] = [ min($line[2], $line[5]), max($line[2], $line[5]) ];

    $cubes[] = [$x1, $x2, $y1, $y2, $z1, $z2];
}

usort($cubes, function($a, $b) {
    return $a[4]-$b[4];
});
collapse($cubes);

$supporting = [];
$supportedBy = [];

foreach ($cubes as $id => $cube) {
    [$x11, $x12, $y11, $y12, $z11, $z12] = $cube;
    $supporting[$id] = [];
    foreach ($cubes as $id2 => $cube2) {
        if ($id2 == $id) { continue; }
        [$x21, $x22, $y21, $y22, $z21, $z22] = $cube2;
        if (
            $x11 <= $x22 && $x12 >= $x21 &&
            $y11 <= $y22 && $y12 >= $y21 &&
            $z12 + 1 == $z21
        ) {
            $supporting[$id][$id2] = $id2;
            $supportedBy[$id2][$id] = $id;
        }
    }
}

foreach ($supporting as $id => $id2s) {
    $disintegratables[$id] = true;
    foreach ($id2s as $id2) {
        if (count($supportedBy[$id2]) == 1) {
            $disintegratables[$id] = false;
            break;
        }
    }
    $s = countSupporting($supporting, $supportedBy, $id);
    $p2 += $s;
}

function countSupporting($supporting, $supportedBy, $id) {
    $v = [];
    $r = 0;
    $q = [$id];
    echo "at $id\n";
    while (is_numeric($c = array_shift($q))) {
        echo "\tat $c\n";
        if (isset($v[$c])) {
            continue;
        }
        $v[$c] = true;
        foreach ($supporting[$c] as $nextId) {
            if (count($supportedBy[$nextId]) <= 1) {
                $q[] = $nextId;
                echo "\t\tat $c adding $nextId\n";
                $r++;
            #} else {
             #   echo "\t\tat $c NOT adding $nextId because " . implode(",", $supportedBy[$nextId]) . "\n";
            }
        }
    }

    return $r;
}

$p1 = count(array_filter($disintegratables));

uasort($cubes, function($a, $b) {
    return $a[4]-$b[4];
});
/*$i = 0;
foreach ($cubes as $id => $cube) {
    if ($disintegratables[$id]) {
        continue;
    }
    $cubesCp = $cubes;
    unset($cubesCp[$id]);
    #$falling = collapse($cubesCp);
    #$p2 += $falling;
    echo $i++ . "/" . count($cubes) . ": $falling\n";
}*/

echo "P1: $p1\nP2: $p2\n";

function collapse(&$cubes) {
    $falling = 0;
    foreach ($cubes as $id => $cube) {
        [$x11, $x12, $y11, $y12, $z11, $z12] = $cube;
        $floor = 0;
        foreach ($cubes as $id2 => $cube2) {
            if ($id2 == $id) {
                continue;
            }
            [$x21, $x22, $y21, $y22, $z21, $z22] = $cube2;
            if (
                $x11 <= $x22 && $x12 >= $x21 &&
                $y11 <= $y22 && $y12 >= $y21 &&
                $z11 > $z22
            ) {
                $floor = max($floor, $z22+1);
                if ($floor == $z11) {
                    continue 2;
                }
            }
        }
        if ($floor < $z11) {
            $cubes[$id][5] = $floor + ($cubes[$id][5] - $cubes[$id][4]);
            $cubes[$id][4] = $floor;
            $falling++;
        }
    }

    return $falling;
}
