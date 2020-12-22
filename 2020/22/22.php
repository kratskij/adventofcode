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
    list($winner, $winnerDeck) = recursiveCombat($decks, $recurse);
    $value = 1;
    $sum = 0;
    while($card = array_pop($winnerDeck)) {
        $sum += $card*$value;
        $value++;
    }

    return $sum;
}

function recursiveCombat(array $decks, $recurse, $level = 0, $round = 1) {
    $knownStates = [];

    while ($decks[0] && $decks[1]) {
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
        }

        echo DEBUG ?  str_repeat(" ", $level) . $round . " (" . $cards[0] . ") " . implode(",", $decks[0]) . "\n" : "";
        echo DEBUG ?  str_repeat(" ", $level) . $round . " (" . $cards[1] . ") " . implode(",", $decks[1]) . "\n" : "";
        echo DEBUG ?  str_repeat(" ", $level) . "---\n" : "";

        if ($recurse && $triggerSubGame) {
            list($winner, $winnerDeck) = recursiveCombat($subDecks, true, $level + 1);
        } else {
            $winner = ($cards[0] > $cards[1]) ? 0 : 1;
        }

        if ($winner == 1) {
            $cards = array_reverse($cards);
        }
        array_push($decks[$winner], ...$cards);
        $round++;
    }

    return [$winner, $decks[$winner]];
}
