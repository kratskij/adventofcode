<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);

$input = $ir->regex("target\sarea\:\sx=([\d\-]+)..([\d\-]+), y=([\d\-]+)..([\d\-]+)");

list($fromX, $toX, $fromY, $toY) = array_map("intval", $input[0]);

$p1 = PHP_INT_MIN;
$p2 = 0;

for ($initVelY = -300; $initVelY < 300; $initVelY++) {
    for ($initVelX = -300; $initVelX < 300; $initVelX++) {
        $y = $x = 0;
        $velY = $initVelY;
        $velX = $initVelX;

        $maxY = PHP_INT_MIN;
        $prevDistance = $prevDistanceDiff = 0;

        while (true) {
            $y += $velY--;
            $x += $velX;
            $velX += ($velX > 0) ? -1 : ($velX < 0 ? 1 : 0);

            $maxY = max($maxY, $y);

            if ($x >= $fromX && $x <= $toX && $y >= $fromY && $y <= $toY) {
                $p1 = max($maxY, $p1);
                $p2++;
                break;
            }

            # if we're increasing our distance away from the grid, we can stop moving
            $distance = abs(min($x-$fromX, $x-$toX)) + abs(min($y-$fromY, $y-$toY));
            $distanceDiff = ($distance-$prevDistance);
            if ($prevDistanceDiff !== 0 && $prevDistance !== 0 && $distance > $prevDistance && $distanceDiff > $prevDistanceDiff) {
                break;
            }
            $prevDistanceDiff = ($distance - $prevDistance);
            $prevDistance = $distance;
        }
    }
}

echo "P1: $p1\nP2: $p2\n";

#4950; too low
#30628


#p2: 639
#p2: 4432: too low
