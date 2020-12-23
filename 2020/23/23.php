<?php

$test = ($argv[1] ?? false == "test");

$cups = array_map("intval", str_split($test ? 389125467 : 872495136));

echo sprintf(
    "P1: %s\nP2: %s\n",
    implode("", solve($cups, ($test) ? 10 : 100, count($cups) - 1)),
    array_product(solve($cups, 10000000, 2, 1000000))
);

function solve($cups, $moveCount, $returnCount, $totalNumberOfCups = null) {
    $max = max($cups);
    if ($totalNumberOfCups !== null && $totalNumberOfCups > count($cups)) {
        $cups = array_merge($cups, range($max + 1, $totalNumberOfCups));
    }

    $links = createLinks($cups);

    $l = count($links);
    $min = min(array_keys($links));
    $max = max(array_keys($links));

    $currentLabel = reset($cups);

    for ($i = 0; $i < $moveCount; $i++) {
        $currentLabel = move($links, $currentLabel, $min, $max);
    }

    $pos = 1;

    $ret = [];
    for ($i = 0; $i < $returnCount; $i++) {
        $pos = $links[$pos]["next"];
        $ret[] = $pos;
    }

    return $ret;
}

function move(&$links, $currentLabel, $min, $max) {
    $firstPick = $links[$currentLabel]["next"];
    $lastPick = $links[$links[$firstPick]["next"]]["next"];

    $destinationLabel = $currentLabel - 1;
    if ($destinationLabel < $min) {
        $destinationLabel = $max;
    }
    while ($destinationLabel == $firstPick || $destinationLabel == $links[$firstPick]["next"] || $destinationLabel == $lastPick) {
        $destinationLabel--;
        if ($destinationLabel < $min) {
            $destinationLabel = $max;
        }
    }
    $destinationNextLabel = $links[$destinationLabel]["next"];

    $links[$currentLabel]["next"] = $links[$lastPick]["next"];
    $links[$lastPick]["prev"] = $currentLabel;

    $links[$destinationLabel]["next"] = $firstPick;
    $links[$firstPick]["prev"] = $destinationLabel;

    $links[$lastPick]["next"] = $destinationNextLabel;
    $links[$destinationNextLabel]["prev"] = $lastPick;

    return $links[$currentLabel]["next"];
}

function createLinks($cups) {
    foreach ($cups as $idx => $c) {
        if (isset($cups[$idx-1])) {
            $prev = $cups[$idx-1];
        } else {
            $prev = $cups[max(array_keys($cups))];
        }

        if (isset($cups[$idx+1])) {
            $next = $cups[$idx+1];
        } else {
            $next = $cups[0];
        }

        $links[$c] = [
            "prev" => $prev,
            "next" => $next
        ];
    }

    return $links;
}
