<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";
const DEBUG = false;

require_once(__DIR__."/../inputReader.php");
$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);

$decks = array_map(
    function($lines) {
        return array_map("intval", array_slice(explode("\n", $lines), 1));
    },
    $ir->explode("\n\n")
);

echo sprintf("P1: %s\nP2: %s\n", solve($decks), solve($decks, true));

function solve($decks, $recurse = false) {
    $winner = recursiveCombat($decks, $recurse);
    $sum = 0;
    foreach (array_reverse($decks[$winner]) as $i => $value) {
        $sum += ($i + 1) * $value;
    }

    return $sum;
}

function recursiveCombat(array &$decks, $recurse, $level = 0, $round = 1) {
    $participants = count($decks);
    $knownStates = [];

    while (count(array_filter($decks)) == count($decks)) {
        $idx = implode("-", array_map(function($deck) { return implode(",", $deck); }, $decks));

        if (isset($knownStates[$idx])) {
            $winner = key($decks);
            break;
        }
        $knownStates[$idx] = true;
        $triggerSubGame = $recurse;
        $subDecks = [];
        foreach ($decks as $id => $deck) {
            $cards[$id] = array_shift($decks[$id]);
            if ($triggerSubGame &= ($cards[$id] <= count($decks[$id]))) {
                $subDecks[$id] = array_slice($decks[$id], 0, $cards[$id]);
            }
            if (DEBUG) {
                echo str_repeat(" ", $level) . $round . " (" . $cards[$id] . ") " . implode(",", $decks[$id]) . "\n";
            }
        }
        echo DEBUG ?  str_repeat(" ", $level) . "---\n" : "";

        if ($recurse && $triggerSubGame) {
            $winner = recursiveCombat($subDecks, true, $level + 1);
        } else {
            $winner = array_search(max($cards), $cards);
        }

        $i = $winner;
        while (isset($cards[$i % $participants])) {
            $decks[$winner][] = $cards[$i % $participants];
            unset($cards[$i%$participants]);
            $i++;
        }

        $round++;
    }

    return $winner;
}
