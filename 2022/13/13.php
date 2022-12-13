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
for ($k = 0; $input; $k++) {
    $packets[] = json_decode(array_shift($input));
    $packets[] = json_decode(array_shift($input));
    $p1 += ($k+1) * cmp($packets[$k*2], $packets[$k*2+1]);

    if ($input) {
        array_shift($input);
    }
}

$packets = array_merge($packets, array_map("json_decode", $dividerPackets));
usort($packets, "cmp");

$p2 = 1;
foreach (array_reverse($packets) as $k => $v) {
    $p2 *= (in_array(json_encode($v), $dividerPackets)) ? $k+1 : 1;
}

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
