<?php

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

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

function lcm(array $args) {
    foreach ($args as $arg) {
        if ($arg == 0) {
            return 0;
        }
    }
    if (empty($args)) {
        return 0;
    }
    if (count($args) == 1) {
        return reset($args);
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
