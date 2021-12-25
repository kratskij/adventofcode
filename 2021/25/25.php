<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);

const SOUTH = "v";
const EAST = ">";
const OPEN = ".";

$current = $ir->grid();

$p1 = 0;
$prev = false;
while (json_encode($current) !== json_encode($prev)) {
    $p1++;
    $prev = $next = $current;
    foreach ([[EAST, 1, 0], [SOUTH, 0, 1]] as $info) {
        list ($dir, $dx, $dy) = $info;
        foreach ($current as $y => $row) {
            $newY = (isset($current[$y+$dy])) ? $y+$dy : 0;
            foreach ($row as $x => $val) {
                if ($val == $dir) {
                    $newX = (isset($current[$y][$x+$dx])) ? $x+$dx : 0;
                    if ($current[$newY][$newX] === OPEN) {
                        $next[$newY][$newX] = $dir;
                        $next[$y][$x] = OPEN;
                    }
                }
            }
        }
        $current = $next;
    }
}

echo "P1: $p1\nP2: PRESS THE LINK!\n";
