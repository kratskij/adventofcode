<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";

$curPos = 0;
$forward = ($test) ? 3 : 324;
$i = 0;

$values = [0];
for($i = 1; $i <= 50000000; $i++) {
    $curPos = ($curPos + $forward + 1) % $i;
    if ($curPos == 0) {
        $last = $i;
    }
    /*if (count($values) > 30) {
        $cp = array_slice($values, max($curPos - 5, 0), 10, true);
    } else {
        $cp = $values;
    }

    $cp[$curPos + 1] = "(" . $cp[$curPos + 1] . ")";
    echo implode("  ", $cp)."\n";*/
    #if (($i % 1000) == 0) {
    #    echo $i."\n";
    #}
    #echo $curPos."\n";
    /*
    #echo $values[$i]."\n";
#    echo array_search(638, $values) . "::".array_search(2017, $values)."\n";
#
    */
}
echo "Part 1: $i\n";
var_Dump(array_slice($values, $curPos - 5,  10, true));

#echo $sum;
#echo $outString;
