<?php

ini_set('memory_limit','2048M');

define("LUMBERYARD", 1);
define("TREES", 2);
define("OPEN", 3);

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$grid = $ir->grid([
    "|" => TREES,
    "#" => LUMBERYARD,
    "." => OPEN
]);

$i = 0;
$repeatInterval = false;
while (true) {
    $i++;
    $gridCopy = $grid;
    foreach ($grid as $y => $row) {
        foreach ($row as $x => $area) {
            $trees = $lumberyards = $openAcres = 0;
            for ($nY = $y-1; $nY <= $y+1; $nY++) {
                for ($nX = $x-1; $nX <= $x+1; $nX++) {
                    if ($nY == $y && $nX == $x) { continue; }
                    if (@$gridCopy[$nY][$nX] == TREES) { $trees++; }
                    if (@$gridCopy[$nY][$nX] == LUMBERYARD) { $lumberyards++; }
                    if (@$gridCopy[$nY][$nX] == OPEN) { $openAcres++; }
                }
            }
            if ($area == OPEN && $trees >= 3) { $grid[$y][$x] = TREES; }
            if ($area == TREES && $lumberyards >= 3) { $grid[$y][$x] = LUMBERYARD; }
            if ($area == LUMBERYARD && ($lumberyards < 1 || $trees < 1)) { $grid[$y][$x] = OPEN; }
        }
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
