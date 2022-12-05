<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file));

$input = $ir->lines();

foreach ($input as $idPos => $line) {
    if (trim($line) == "") {
        break;
    }
}
$idPos = $idPos -1;

$charStackMap = [];
for ($i = 0; $i < $idPos; $i++) {
    $line = $input[$i];
    foreach (str_split($line) as $col => $char) {
        if (($col % 4) != 1 || trim($char) == "") {
            continue;
        }
        $stackId = str_split($input[$idPos])[$col];
        if (!isset($stacks[$stackId])) {
            $stacks[$stackId] = [];
        }
        array_unshift($stacks[$stackId], $char);
        $charStackMap[$char] = $stackId;
    }
}
foreach ($stacks as $i => $stack) {
    $stacks[$i] = array_filter(array_map("trim", $stacks[$i]));
}
ksort($stacks);
$p1 = $p2 = "";

foreach ([1 => &$p1, 2 => &$p2] as $part => &$res) {
    $pStacks = $stacks;
    for ($i = $idPos+1; $i < count($input); $i++) {
        $line = $input[$i];
        if (trim($line) == "") {
            continue;
        }
        list($move, $num, $from, $fromStack, $to, $toStack) = explode(" ", $line);
        $toStack = trim($toStack);
        $poof = [];
        for ($j = 0; $j < $num; $j++) {
            $poof[] = array_pop($pStacks[$fromStack]);
        }
        if ($part == 2) {
            $poof = array_reverse($poof);
        }
        foreach ($poof as $p) {
            $pStacks[$toStack][] = $p;
        }
    }
    foreach ($pStacks as $stack) {
        $res .= array_pop($stack);
    }
}

echo "P1: $p1\nP2: $p2\n";
