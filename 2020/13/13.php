<?php

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");
require_once(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."/Toolbox.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$lines = $ir->lines();
$p1 = $p2 = false;

$earliest = (int)$lines[0];

$buses = [];
$offset = 0;
foreach (explode(",", $lines[1]) as $i) {
    if ($i != "x") {
        $buses[$i] = $offset;
    }
    $offset++;
}

$i = $earliest;
$increment = 1;

$matches = [];
while (!$p1 || !$p2) {
    foreach ($buses as $busId => $busOffset) {
        if (!$p1) {
            if (($i % $busId) == 0) {
                $p1 = ($i - $earliest) * $busId;
            }
        } else {
            if ((($i + $busOffset) % $busId) == 0) {
                $matches[$busId] = $busId;
            }
        }
    }
    $increment = max($increment, lcm($matches));
    if (count($matches) == count($buses)) {
        $p2 = $i;
    }
    $i += $increment;
}

echo "P1: $p1\nP2: $p2\n";
