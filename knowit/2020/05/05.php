<?php

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim();
$input = $ir->chars();

$house = [];
$y = $x = $yd = $xd = 0;

foreach ($input as $i) {
    foreach (range(0,1) as $null) {
        switch ($i) {
            case "H": //høyre
                $x += 1;
                $xd = 1;
                break;
            case "V": //venstre
                $x -= 1;
                $xd = -1;
                break;
            case "O": //opp
                $y += 1;
                $yd = 1;
                break;
            case "N": //ned
                $y -= 1;
                $yd = -1;
                break;
        }
        $house[$y][$x] = true;
    }
}

$dirs = [[0,1], [0,-1], [1,0], [-1,0]];
$visited = [];
$queue = [ [$y-$yd, $x-$xd] ]; // let's start inside the house (by going one step in the opposite direction of where we came from)

$c = 0;
while ($pos = array_pop($queue)) {
    list($y, $x) = $pos;
    $visited[$y][$x] = true;
    foreach ($dirs as $dir) {
        list($dy, $dx) = $dir;
        if (!isset($house[$y+$dy][$x+$dx]) && !isset($visited[$y+$dy][$x+$dx])) {
            $queue[($y+$dy)."_".($x+$dx)] = [$y+$dy, $x+$dx];
        }
    }
}

$ymin = min(array_keys($visited));
$ymax = max(array_keys($visited));
$xmin = min(array_map("min", array_map("array_keys", $visited)));
$xmax = max(array_map("max", array_map("array_keys", $visited)));

$area = 0;
for ($y = $ymax; $y >= $ymin; $y -= 2) {
    for ($x = $xmin; $x <= $xmax; $x += 2) {
        if (isset($visited[$y][$x])) {
            #echo "░";
            $area++;
        } else {
            #echo " ";
        }
    }
    #echo "\n";
}
echo $area."\n";
