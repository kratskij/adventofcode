<?php

$input = 3014387;
$elves = [];

for ($i = 1; $i <= $input; $i++) {
    $elves[$i] = $i;
}

echo "Part 1: " . reduceByNeighbor($elves) . "\n";
echo "Part 2: " . reduceByOpposite($elves) . "\n";

//Part 1
function reduceByNeighbor(array $elves)
{
    while (count($elves) > 1) {
        forward($elves);
        $deleteIndex = (key($elves) === null) ? reset($elves) : key($elves);

        //stand back; things are about to get crazy in here!
        prev($elves);
        deleteIndex($elves, $deleteIndex);
    }

    return reset($elves);
}

// Part 2
function reduceByOpposite(array $real)
{
    $opposite = $real;
    for ($i = 0; $i < floor(count($opposite) / 2); $i++ ) {
        next($opposite);
    }

    while (count($real) > 1) {
        $deleteIndex = (key($opposite) === null) ? reset($opposite) : key($opposite);

        //stand back; things are about to get crazy in here!
        prev($opposite);

        deleteIndex($real, $deleteIndex);
        deleteIndex($opposite, $deleteIndex);

        //compensate for reduced array (if input is an even number, skip after odd numbers, and vice versa)
        if (count($opposite) % 2 == 0) {
            forward($opposite);
        }
    }
    return reset($real);
}

function deleteIndex(&$arr, $idx) {
    unset($arr[$idx]);
    forward($arr);
}

function forward(&$arr) {
    $i = next($arr);
    if ($i === false) {
        $i = reset($arr);
    }
}
