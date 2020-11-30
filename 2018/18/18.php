<?php

ini_set('memory_limit','2048M');

define("OPEN", 0);
define("LUMBERYARD", 1);
define("TREES", 2);

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);

$charMap = [
     TREES => "|",
     LUMBERYARD => "#",
     OPEN => "."
];

$grid = $ir->grid(array_flip($charMap));
$neighbours = [];
foreach ($grid as $y => $row) {
    foreach ($row as $x => $char) {
        $neighbours[$y][$x] = [];
        for ($nY = $y-1; $nY <= $y+1; $nY++) {
            for ($nX = $x-1; $nX <= $x+1; $nX++) {
                if ($nY == $y && $nX == $x || !isset($grid[$nY][$nX])) { continue; }
                $neighbours[$y][$x][] = &$grid[$nY][$nX];
            }
        }
    }
}

$i = 0;
$repeatInterval = false;

while (true) {
    $i++;
    $gridCopy = [];
    foreach ($grid as $y => $row) {
        foreach ($row as $x => $val) {
            $gridCopy[$y][$x] = $val;
        }
    }
    foreach ($grid as $y => $row) {
        foreach ($row as $x => $area) {
            $trees = $lumberyards = 0;
            foreach ($neighbours[$y][$x] as $neighbour) {
                if ($neighbour == TREES) {
                    $trees++;
                } else if ($neighbour == LUMBERYARD) {
                    $lumberyards++;
                }
            }
            if ($area == OPEN && $trees >= 3) { $gridCopy[$y][$x] = TREES; }
            if ($area == TREES && $lumberyards >= 3) { $gridCopy[$y][$x] = LUMBERYARD; }
            if ($area == LUMBERYARD && ($lumberyards == 0 || $trees == 0)) { $gridCopy[$y][$x] = OPEN; }
        }
    }

    foreach ($grid as $y =>$row) {
        foreach ($row as $x =>$char) {
            $grid[$y][$x] = $gridCopy[$y][$x];
            #echo $charMap[$grid[$y][$x]];
        }
        #echo "\n";
    }

    if (!$repeatInterval) {
        $hash = md5(implode(array_map("implode", $grid)));
        if (isset($hashes[$hash])) { // We've been here before.
            $repeatInterval = $i - $hashes[$hash];
        }
        $hashes[$hash] = $i;
    }

    if ($repeatInterval && ($i % $repeatInterval) == (1000000000 % $repeatInterval)) {
        echo "Part 2: " .  resourceValue($grid) . "\n";
        break;
    }

    if ($i == 10) {
        echo "Part 1: " .  resourceValue($grid) . "\n";
    }
}

function resourceValue($grid) {
    $wooden = $lumberyards = 0;
    foreach ($grid as $row) {
        foreach ($row as $char) {
            if ($char == LUMBERYARD) {
                $lumberyards++;
            } else if ($char == TREES) {
                $wooden++;
            }
        }
    }
    return $wooden*$lumberyards;

}
