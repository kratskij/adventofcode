<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");
require_once(__DIR__."/../Util.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->lines();

$x = 1;
$ticks = 0;
$p1 = 0;

foreach ($input as $k => $line) {
    $parts = explode(" ", $line);
    $cmd = array_shift($parts);


    printScreen($ticks, $x);
    $ticks++;
    $p1 += signalStrength($ticks, $x);
    switch ($cmd) {
        case "addx":
            $value = array_shift($parts);

            printScreen($ticks, $x);
            $ticks++;
            $p1 += signalStrength($ticks, $x);
            $x += (int)$value;
            if (!is_numeric($value)) {
                throw new \Exception("not int");
            }
            break;
        case "noop":
            break;
    }
}

$p2 = "\n".printScreen($ticks, $x);

echo "P1: $p1\nP2: $p2\n";

function signalStrength($ticks, $x) {
    if ((($ticks - 20) % 40) == 0) {
        return $ticks*$x;
    }

    return 0;
}

function printScreen($ticks, $x) {
    static $grid;
    if ($grid === null) {
        for ($y = 0; $y < 6; $y++) {
            for ($x = 0; $x < 40; $x++) {
                $grid[$y][$x] = false;
            }
        }
    }

    $drawY = floor($ticks / 40);
    $drawX = ($ticks % 40);
    if ($drawX - $x >= -1 && $drawX - $x <= 1) {
        $symbol = true;
    } else {
        $symbol = false;
    }
    if (isset($grid[$drawY][$drawX])) {
        $grid[$drawY][$drawX] = $symbol;
    }

    return Util::printGrid($grid);
}
