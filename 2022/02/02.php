<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->csv(" ");

const ROCK = 1;
const PAPER = 2;
const SCISSOR = 3;

const LOSS = 0;
const DRAW = 3;
const WIN = 6;

$himMap = [ "A" => ROCK, "B" => PAPER, "C" => SCISSOR, ];
$meMap = [ "X" => [ROCK, LOSS], "Y" => [PAPER, DRAW], "Z" => [SCISSOR, WIN] ];

$beats = [
    ROCK => SCISSOR,
    SCISSOR => PAPER,
    PAPER => ROCK,
];
$beaten = array_flip($beats);

$p1 = $p2 = 0;
foreach ($input as $k => $line) {
    list($him, $me) = $line;
    $him = $himMap[$him];

    $choice = $meMap[$me][0];
    if ($beats[$choice] == $him) {
        $outcome = WIN;
    } else if ($beaten[$choice] == $him) {
        $outcome = LOSS;
    } else {
        $outcome = DRAW;
    }
    $p1 += $choice + $outcome;

    $outcome = $meMap[$me][1];
    if ($outcome == LOSS) {
        $choice = $beats[$him];
    } else if ($outcome == WIN) {
        $choice = $beaten[$him];
    } else {
        $choice = $him;
    }
    $p2 += $choice + $outcome;
}

echo "P1: $p1\nP2: $p2\n";
