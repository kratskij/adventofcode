<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->lines();

$list = [];
foreach ($input as $k => $line) {
    list($id, $value) = explode(": ", $line);
    $list[$id] = (is_numeric($value)) ? (int)$value : explode(" ", $value);
}
echo "P1: " . solve($list)["root"] . "\n";

$list["root"][1] = "=";
$list["humn"] = 0;
while (true) {
    $factor = solve($list)["root"];
    if ($factor == 1) {
        $p2 = $list["humn"];
        break;
    } else if ($factor !== INF) {
        $list["humn"] = floor($list["humn"] / $factor);
    }
    $list["humn"]++;
}
echo "P2: {$list["humn"]}\n";

function solve($list) {
    while (!is_numeric($list["root"])) {
        foreach ($list as $id => $value) {
            if (is_numeric($value)) {
                continue;
            } else {
                list($id1, $op, $id2) = $value;
                if (
                    isset($list[$id1]) && is_numeric($list[$id1]) &&
                    isset($list[$id2]) && is_numeric($list[$id2])
                ) {
                    if ($op == "+") {
                        $list[$id] = $list[$id1] + $list[$id2];
                    } else if ($op == "-") {
                        $list[$id] = $list[$id1] - $list[$id2];
                    } else if ($op == "*") {
                        $list[$id] = $list[$id1] * $list[$id2];
                    } else if ($op == "/") {
                        $list[$id] = $list[$id1] / $list[$id2];
                    } else if ($op == "=") {
                        $list[$id] = ($list[$id1]) ? $list[$id2] / $list[$id1] : INF;
                    }
                }
            }
        }
    }

    return $list;
}
