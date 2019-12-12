<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file);

$input = $ir->trim(true)->regex("\<x=(\-?\d+),\sy=(\-?\d+),\sz=(\-?\d+)\>");

$moons = [];
foreach ($input as $line) {
    $moon = [];
    $moon["x"] = intval($line[0]);
    $moon["y"] = intval($line[1]);
    $moon["z"] = intval($line[2]);
    $moon["v"] = [
        "x" => 0,
        "y" => 0,
        "z" => 0
    ];
    $moons[] = $moon;
}
echo "Part 1: " . energyAt($moons, 1000) . "\n";
echo "Part 2: " . repeatsAt($moons, ["x", "y", "z"]) . "\n";

function tick(&$moons, $axis) {
    $moonCount = count($moons);
    foreach ($moons as $id => $moon) {
        for ($nid = $id + 1; $nid < $moonCount; $nid++) {
            if ($moons[$nid][$axis] != $moons[$id][$axis]) {
                $moons[$id]["v"][$axis] += ($moons[$id][$axis] > $moons[$nid][$axis]) ? -1 : 1;
                $moons[$nid]["v"][$axis] += ($moons[$nid][$axis] > $moons[$id][$axis]) ? -1 : 1;
            }
        }
        $moons[$id][$axis] += $moons[$id]["v"][$axis];
    }

    return true;
}

function energyAt($moons, $steps) {
    $moonCount = count($moons);
    for ($i = 0; $i < 1000; $i++) {
        tick($moons, "x");
        tick($moons, "y");
        tick($moons, "z");
    }

    $energy = 0;
    foreach ($moons as $m) {
        $pot = abs($m["x"]) + abs($m["y"]) + abs($m["z"]);
        $kin = abs($m["v"]["x"]) + abs($m["v"]["y"]) + abs($m["v"]["z"]);
        $energy += $pot * $kin;
    }
    return $energy;
}

function repeatsAt($moons, $axes) {
    $cycleCounts = [];
    foreach ($axes as $axis) {
        $beginState = stringify($moons, $axis);

        $cycleCounts[$axis] = 1;
        while (tick($moons, $axis) && stringify($moons, $axis) != $beginState) {
            $cycleCounts[$axis]++;
        }
    }

    return lcm($cycleCounts);
}

function stringify($moons, $axis) {
    return implode(
        ",",
        array_merge(
            array_column($moons, $axis),
            array_column(array_column($moons, "v"), $axis)
        )
    );
}

function lcm(array $args) {
    foreach ($args as $arg) {
        if ($arg == 0) {
            return 0;
        }
    }
    if (count($args) == 2) {
        $m = array_shift($args);
        $n = array_shift($args);
        return abs(($m * $n) / gcd($m, $n));
    }

    return lcm(array_merge([array_shift($args)], [lcm($args)]));
}

function gcd($a, $b) {
   while ($b != 0) {
       $t = $b;
       $b = $a % $b;
       $a = $t;
   }
   return $a;
}


function p($moons, $steps) {
    echo $steps." steps\n";
    foreach ($moons as $m) {
        echo "pos=<x={$m['x']}, y={$m['y']}, z={$m['z']}>, vel=<x={$m['v']['x']}, y={$m['v']['y']}, z={$m['v']['z']}>\n";
    }
    echo "\n";
}
