<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";

$file = ($test) ? "test" : "input";
$input = explode("\n", trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file)));
#$input = str_split($input[0]);
#$input = preg_match ("//" , $input, &$matches)
#var_dump($input);

$regex = "//";
$values = [];
$outString = "";
$sum = 0;
$c = 0;

$origGrid = [];

foreach ($input as $k => $line) { #$matches?
    $props = explode(", ", $line);
    $pix = [];
    foreach ($props as $p) {
            $p = str_replace("<", "", $p);
            $p = str_replace("<", "", $p);
            $p = explode("=", $p);
            $pix[$p[0]] = array_map("intval", explode(",", $p[1]));
    }
    $idx = $pix["p"][0] . "_" . $pix["p"][1] . "_" . $pix["p"][2];

    $pix["id"] = $k;
    $origGrid[$idx] = $pix;
}

const IN_THE_LONG_RUN = 1000;

$grid = $origGrid;
$c = 0;

while (true) {
    $c++;
    $minDist = INF;
    $minDiff = INF;

    foreach ($grid as $k => &$p) {
        $prevDist = array_sum(array_map("abs", $p["p"]));

        $p["v"][0] += $p["a"][0];
        $p["v"][1] += $p["a"][1];
        $p["v"][2] += $p["a"][2];
        $p["p"][0] += $p["v"][0];
        $p["p"][1] += $p["v"][1];
        $p["p"][2] += $p["v"][2];

        if ($c > IN_THE_LONG_RUN) {
            $newDist = array_sum(array_map("abs", $p["p"]));
            $diff = $newDist - $prevDist;
            if ($newDist < $minDist) {
                $minDist = $newDist;
                $minDistId = $k;
            }
            if ($diff < $minDiff) {
                $minDiff = $diff;
                $minDiffId = $k;
            }
        }
    }
    if ($c > IN_THE_LONG_RUN && $grid[$minDistId]["id"] == $grid[$minDiffId]["id"]) {
        echo "Part 1: " . $grid[$minDistId]["id"] . "\n";
        break;
    }
}

$lastDestroy = 0;
$grid = $origGrid;

while (true) {
    $newGrid = [];
    $destroy = [];
    $lastDestroy++;
    foreach ($grid as $k => &$p) {
        $p["v"][0] += $p["a"][0];
        $p["v"][1] += $p["a"][1];
        $p["v"][2] += $p["a"][2];
        $p["p"][0] += $p["v"][0];
        $p["p"][1] += $p["v"][1];
        $p["p"][2] += $p["v"][2];

        $idx = $p["p"][0] . "_" . $p["p"][1] . "_" . $p["p"][2];

        if (isset($newGrid[$idx])) {
            $destroy[] = $idx;
            unset($newGrid[$idx]);
        } else {
            $newGrid[$idx] = $p;
        }
    }
    if ($destroy) {
        foreach ($destroy as $dest) {
            unset($newGrid[$dest]);
        }
        $lastDestroy = 0;
    }

    $grid = $newGrid;

    if ($lastDestroy > IN_THE_LONG_RUN) {
        echo "Part 2: " . count($grid) . "\n";
        break;
    }
}
