<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim();
$cmds = $ir->regex("(off|on)\sx=([\d\-]+)..([\d\-]+),y=([\d\-]+)..([\d\-]+),z=([\d\-]+)..([\d\-]+)");

$p2 = 0;
foreach ($cmds as $k => $cmd) {
    $affecting = affecting($cmds, $k);
    $p2 += $affecting;
}

$p1 = -affecting($cmds, count($cmds), [false, -50, 50, -50, 50, -50, 50]);
echo "P1: $p1\nP2: $p2\n";

function affecting($cmds, $k, $subCube = false) {
    if ($subCube) {
        list($state, $fromX, $toX, $fromY, $toY, $fromZ, $toZ) = $subCube;
    } else {
        $cmd = $cmds[$k];
        list($state, $fromX, $toX, $fromY, $toY, $fromZ, $toZ) = $cmd;
    }
    $size = ($toX-$fromX+1)*($toY-$fromY+1)*($toZ-$fromZ+1);

    $overlaps = 0;
    for ($i = $k-1; $i >= 0; $i--) {
        $cmd2 = $cmds[$i];
        list($state2, $fromX2, $toX2, $fromY2, $toY2, $fromZ2, $toZ2) = $cmd2;
        $fromXd = max($fromX, $fromX2);
        $fromYd = max($fromY, $fromY2);
        $fromZd = max($fromZ, $fromZ2);
        $toXd = min($toX, $toX2);
        $toYd = min($toY, $toY2);
        $toZd = min($toZ, $toZ2);

        if ($fromXd > $toXd || $fromYd > $toYd || $fromZd > $toZd) {
            continue;
        }

        $overlaps += affecting($cmds, $i, [$state2, $fromXd, $toXd, $fromYd, $toYd, $fromZd, $toZd]);
    }

    return ($state == "on") ? $size - $overlaps : -$overlaps;
}
