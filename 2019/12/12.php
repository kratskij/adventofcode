<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file);

$input = $ir->trim(true)->regex("\<x=(\-?\d+),\sy=(\-?\d+),\sz=(\-?\d+)\>");

$moons = [];
foreach ($input as $i) {
    $moon = [];
    $moon["x"] = intval($i[0]);
    $moon["y"] = intval($i[1]);
    $moon["z"] = intval($i[2]);
    $moon["v"] = [
        "x" => 0,
        "y" => 0,
        "z" => 0
    ];
    $moons[] = $moon;
}

$moonCount = count($moons);

$states = [];

$i = 0;
$hashBase = "";
foreach ($moons as $moon) {
    $hashBase .= "{$moon['x']},{$moon['y']},{$moon['z']},";
}
$firstHash = $hashBase;

while(true) {
    $allZero = true;
    foreach ($moons as $id => $moon) {
        //apply gravity
        for ($nid = $id+1; $nid < $moonCount; $nid++) {
            if ($moons[$nid]["x"] != $moons[$id]["x"]) {
                $moons[$id]["v"]["x"] += ($moons[$id]["x"] > $moons[$nid]["x"]) ? -1 : 1;
                $moons[$nid]["v"]["x"] += ($moons[$nid]["x"] > $moons[$id]["x"]) ? -1 : 1;
            }
            if ($moons[$nid]["y"] != $moons[$id]["y"]) {
                $moons[$id]["v"]["y"] += ($moons[$id]["y"] > $moons[$nid]["y"]) ? -1 : 1;
                $moons[$nid]["v"]["y"] += ($moons[$nid]["y"] > $moons[$id]["y"]) ? -1 : 1;
            }
            if ($moons[$nid]["z"] != $moons[$id]["z"]) {
                $moons[$id]["v"]["z"] += ($moons[$id]["z"] > $moons[$nid]["z"]) ? -1 : 1;
                $moons[$nid]["v"]["z"] += ($moons[$nid]["z"] > $moons[$id]["z"]) ? -1 : 1;
            }
        }

        //update position
        $moons[$id]["x"] += $moons[$id]["v"]["x"];
        $moons[$id]["y"] += $moons[$id]["v"]["y"];
        $moons[$id]["z"] += $moons[$id]["v"]["z"];

        $allZero = $allZero && $moons[$id]["v"]["x"] === 0 && $moons[$id]["v"]["y"] === 0 && $moons[$id]["v"]["z"] === 0;
    }
    $i++;
    if ($allZero) {
        p($moons, $i);
        $hashBase = "";
        foreach ($moons as $moon) {
            $hashBase .= "{$moon['x']},{$moon['y']},{$moon['z']},";
        }

        if ($firstHash === $hashBase) {
            echo "BREAK AT $i\n";
            break;
        }
    }


}

echo "Part 1: " .energy($moons) . "\n";

function energy($moons) {
    $energies = [];
    foreach ($moons as $m) {
        $pot = abs($m["x"]) + abs($m["y"]) + abs($m["z"]);
        $kin = abs($m["v"]["x"]) + abs($m["v"]["y"]) + abs($m["v"]["z"]);
        $energies[] = $pot * $kin;
        #var_Dump($pot, $kin);
    }
    return array_sum($energies);
}

function p($moons, $steps) {
    echo $steps." steps\n";
    foreach ($moons as $m) {
        echo "pos=<x={$m['x']}, y={$m['y']}, z={$m['z']}>, vel=<x={$m['v']['x']}, y={$m['v']['y']}, z={$m['v']['z']}>\n";
    }
    echo "\n";
}
