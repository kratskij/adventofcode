<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");
require_once(__DIR__."/../Util.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$jets = $ir->chars();

$width = 7;
$rocks = [
    [
        0 => [0,1,2,3],
    ],
    [
        0 =>   [1],
        1 => [0,1,2],
        2 =>   [1],
    ],
    [
        0 =>     [2],
        1 =>     [2],
        2 => [0,1,2],
    ],
    [
        0 => [0],
        1 => [0],
        2 => [0],
        3 => [0],
    ],
    [
        0 => [0,1],
        1 => [0,1],
    ],
];

// grid has baseline at y=0, and all rows above are _negative_ because of reasons
$grid = [
    array_fill_keys(range(0, $width-1), true),
];

$jetIdx = 0;
$l = count($jets);

$currentRockIdx = 0;
$rock = $rocks[0];
$rockY = -4 - max(array_keys($rock));
$rockX = 2;
$rockWidth = max(array_map("max", $rock)) + 1;

$stopAt = 2022;
$p2stopAt = 1000000000000;
$states = [];

while(true) {
    $jetX = $jets[$jetIdx % $l] == "<" ? -1 : 1;
    if ($rockX+$jetX < 0 || $rockX+$rockWidth+$jetX >= 7) {
        $jetX = 0; //no jet
    } else {
        if (isAvailable($rock, $rockY, $rockX+$jetX, $grid)) {
            $rockX += $jetX;
        }
    }
    $jetIdx++;

    if (isAvailable($rock, $rockY+1, $rockX, $grid)) {
        $rockY++;
    } else {
        // place rock
        foreach ($rock as $cy => $cxs) {
            foreach ($cxs as $cx) {
                $grid[$rockY+$cy][$rockX+$cx] = true;
            }
        }
        // select new rock
        $currentRockIdx++;
        $rock = $rocks[$currentRockIdx % 5];
        $rockY = min(array_keys($grid)) - 4 - max(array_keys($rock));
        $rockX = 2;
        $rockWidth = max(array_map("max", $rock));

        if ($currentRockIdx == $stopAt) {
            $p1 = -min(array_keys($grid));
        }

        /* we need to look for repeatable patterns, by storing the state:
         * - relative position of each column's top element
         * - next rock
         * - jet index
         */

        $top = min(array_keys($grid));
        $current = $top;
        $topLine = "";
        $tops = [];
        while (count($tops) != 7 && $current < 0) {
            for ($i = 0; $i < 7; $i++) {
                if (!isset($tops[$i]) && isset($grid[$current][$i])) {
                    $tops[$i] = $top-$current;
                }
            }
            $current++;
        }

        if (count($tops) == 7) {
            // we have filled all columns
            $topLine = implode("_", $tops);
            $stateIdx = $topLine."_".($currentRockIdx % 5)."_".($jetIdx%$l);

            if (!isset($states[$stateIdx])) {
                // a new state; store it!
                $states[$stateIdx] = [
                    "height" => -min(array_keys($grid)),
                    "count" => count($states),
                    "rocks" => $currentRockIdx,
                ];
                $stateCounts2[] = $stateIdx;
            } else {
                // OMG we found a known state! time to calculate the number of repetitions
                $fromStateIdx = $stateCounts2[$states[$stateIdx]["count"]];
                $toState = [
                    "height" => -min(array_keys($grid)),
                    "count" => count($states),
                    "rocks" => $currentRockIdx,
                ];

                $repeatLength = $toState["rocks"] - $states[$fromStateIdx]["rocks"];
                $repetitions = floor(($p2stopAt - $states[$fromStateIdx]["rocks"]) / $repeatLength);
                $remainder = $p2stopAt - $states[$fromStateIdx]["rocks"] - ($repetitions * $repeatLength);
                $endStateIdx = $stateCounts2[$states[$fromStateIdx]["count"] + $remainder];
                $repeatHeight = $toState["height"] - $states[$fromStateIdx]["height"];

                $beforeHeight = $states[$fromStateIdx]["height"];
                $repeatsHeight = $repeatHeight * $repetitions;
                $remainderHeight = ($states[$endStateIdx]["height"]-$states[$fromStateIdx]["height"]);

                $p2 = $beforeHeight + $repeatsHeight + $remainderHeight;
                break;
            }
            if (count($states) % 10000 == 0) {
                echo count($states)."\n";
            }
        }
    }
}

echo "P1: $p1\nP2: $p2\n";

function isAvailable($rock, $rockY, $rockX, &$grid) {
    foreach ($rock as $cy => $cxs) {
        foreach ($cxs as $cx) {
            if (isset($grid[$rockY+$cy][$rockX+$cx])) {
                return false;
            }
        }
    }
    return true;
}
