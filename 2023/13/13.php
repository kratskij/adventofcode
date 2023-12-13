<?php

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$groups = $ir->explode("\n\n");

$p1 = $p2 = 0;

foreach ($groups as $k => $group) {
    $lines = explode("\n", $group);
    $p1Val = findValue($lines, [false,false]);
    $p1 += ($p1Val[0] * 100) + $p1Val[1];

    for ($rep = 0; $rep < strlen($group); $rep++) {
        $groupCopy = $group;
        if ($groupCopy[$rep] == ".") {
            $groupCopy[$rep] = "#";
        } else if ($groupCopy[$rep] == "#") {
            $groupCopy[$rep] = ".";
        } else {
            continue;
        }
        $lines = explode("\n", $groupCopy);

        $p2Val = findValue($lines, $p1Val);
        if (array_filter($p2Val)) {
            $p2 += ($p2Val[0] * 100) + $p2Val[1];
            break;
        }
    }
}

echo "P1: $p1\nP2: $p2\n";

function findValue($lines, $invalid) {
    $w = strlen($lines[0]);
    $transposed = [];
    for ($i = 0; $i < $w; $i++) {
        $newLine = "";
        foreach ($lines as $line) {
            $newLine .= $line[$i];
        }
        $transposed[] = $newLine;
    }

    $ret = [0,0];
    
    if ($row = findRefPos($lines, $invalid[0])) {
        $ret[0] = $row;
    }
    if ($col = findRefPos($transposed, $invalid[1])) {
        $ret[1] = $col;
    }

    return $ret;
}

function findRefPos($lines, $invalid) {
    $h = count($lines);
    $notRows = [];
    for ($i = 0; $i < $h; $i++) {
        $len = min($i+1, $h-1-$i);
        $before = array_slice($lines, $i-$len+1, $len);
        $after = array_reverse(array_slice($lines, $i+1, $len));
        if ($before != $after) {
            $notRows[$i] = $i;
        }
    }

    $rowCandidates = array_diff(range(0, $h-2), array_keys($notRows));
    $rowCandidates = array_filter($rowCandidates, function($r) use ($invalid) { return $r + 1 != $invalid; });
    if ($rowCandidates) {
        return reset($rowCandidates) + 1;
    }

    return false;
}
