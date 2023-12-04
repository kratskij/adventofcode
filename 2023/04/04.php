<?php

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);

$input = $ir->regex("^Card\s+(\d+):\s+([\d\s]+)\s\|\s([\d\s]+)$");

$p1wins = $copies = [];
foreach ($input as $k => $line) {
    [$card, $winning, $nums] = $line;

    $winning = array_map("intval", preg_split("/\s+/", $winning));
    $nums = array_map("intval", preg_split("/\s+/", $nums));

    foreach ($nums as $num) {
        if (in_array($num, $winning)) {
            $p1wins[$card][] = 1;
        }
    }

    $copies[$card] = 1;
}

$p1 = 0;
foreach ($p1wins as $card => $win) {
    $p1 += pow(2, count($win) - 1);

    for ($i = $card + 1; $i <= $card + count($win); $i++) {
        $copies[$i] += $copies[$card];
    }
}
$p2 = array_sum($copies);

echo "P1: $p1\nP2: $p2\n";
