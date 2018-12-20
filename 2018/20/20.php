<?php

require_once(__DIR__."/../inputReader.php");
require_once(__DIR__."/../../Graph.php");

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->chars();

array_shift($input); // remove ^ at beginning of "regex" ...
array_pop($input);  // remove $ at end of "regex" ...

/* let's build a structure of the rooms:
 *
 * example room design:
 * ###
 * #6#
 * #-#####
 * #4|3|5#
 * #-#-###
 * #1|2#
 * #####
 *
 * This would become the following input "regex":
 * EN(W(S|N)|E)
 *
 * Which should result in the following structure provided to the Graph class:
 * room 1 -> 2 (and 4, later, but handle by the doubly linked list in the Graph class)
 * room 2 -> 3
 * room 3 -> 4 and 5
 * room 4 -> 1 and 6
*/

$structure = [];
$y = $x = 0;
$stack = [[$y, $x]];

foreach ($input as $char) {
    $currIdx = $y . "_" . $x;
    switch ($char) {
        case "E":
            $x++;
            break;
        case "S":
            $y++;
            break;
        case "W":
            $x--;
            break;
        case "N":
            $y--;
            break;
        case "(":
            array_push($stack, [$y, $x]);
            continue 2; // go to next char
        case ")":
            list($y, $x) = array_pop($stack);
            continue 2; // go to next char
        case "|":
            list($y, $x) = end($stack);
            continue 2; // go to next char
        default:
            throw new Exception("WAT");
    }
    $structure[$currIdx][] = $y . "_" . $x;
}

$g = new Graph($structure, "0_0");
echo "Part 1: " . $g->maxShortestPath() . "\n";
echo "Part 2: " . count($g->filterDistances(function ($n) { return $n >= 1000; })) . "\n";
