<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->lines();
$dividerPackets = [
    "[[2]]",
    "[[6]]"
];

$p1 = 0;
$packets = [];
for ($k = 1; $input; $k++) {
    $packets[$k*2-1] = json_decode(array_shift($input));
    $packets[$k*2] = json_decode(array_shift($input));
    $p1 += $k * cmp($packets[$k*2-1], $packets[$k*2]);

    if ($input) {
        array_shift($input);
    }
}

foreach ($dividerPackets as $p) {
    $packets[] = json_decode($p);
}

usort($packets, "cmp");
$packets = array_reverse($packets);
array_unshift($packets, ""); // make the actual packets start at key 1

$p2 = array_product(array_keys(array_filter(
    $packets,
    function($p) use ($dividerPackets) {
        return in_array(json_encode($p), $dividerPackets);
    }
)));

echo "P1: $p1\nP2: $p2\n";

function cmp($left, $right) {
    if (is_numeric($left) && is_numeric($right)) {
        if ($left < $right) {
            return true;
        } else if ($right < $left) {
            return false;
        }
        return null;
    } else if (is_numeric($left)) {
        $left = [$left];
    } else if (is_numeric($right)) {
        $right = [$right];
    }
    if (is_array($left) && is_array($right)) {
        $keys = array_unique(array_merge(array_keys($left), array_keys($right)));
        foreach ($keys as $k) {
            if (!isset($left[$k])) {
                return true;
            }
            if (!isset($right[$k])) {
                return false;
            }
            $res = cmp($left[$k], $right[$k]);
            if ($res !== null) {
                return $res;
            }
        }
    }
}
