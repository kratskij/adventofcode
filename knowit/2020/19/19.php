<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->regex("(\d+)\s(\d+)\s\[(.*)\]");

$debug = false;
$winners = [];
foreach ($input as $k => $line) {
    list($rule, $move, $elves) = $line;
    $elves = explode(", ", $elves);
    $remaining = count($elves);
    while ($remaining > 1) {
        $pos = -$move;
        while($pos < 0 ) {
            $pos += $remaining;
        }
        echo $debug ? "[" . implode(", ", $elves). "] -> " : "";
        $elves = array_merge(
            array_slice($elves, $pos),
            array_slice($elves, 0, $pos)
        );
        echo $debug ? "[" . implode(", ", $elves). "] " : "";
        switch ($rule) {
            case 1:
                $remove = [0];
                break;
            case 2:
                $rel = (isset($rel)) ? ($rel + 1) % $remaining : -1;
                $remove = [$rel];
                break;
            case 3:
                $mid = $remaining / 2;
                if ($remaining == 2) {
                    $remove = [0];
                } else {
                    $remove = ($mid == (int)$mid) ? [$mid - 1, $mid] : [$mid - .5];
                }
                break;
            case 4:
                $remove = [max(array_keys($elves))];
                break;
        }
        echo $debug ? "[" . implode("][", $remove) . "] " . implode(" og ", array_map(function($rem) use ($elves) { return $elves[$rem]; }, $remove)) . " ryker ut\n" : "";
        foreach ($remove as $r) {
            unset($elves[$r]);
        }
        $remaining -= count($remove);
    }

    $winner = reset($elves);
    echo $debug ? "$winner won!\n" : "";
    if (!isset($winners[$winner])) {
        $winners[$winner] = 0;
    }
    $winners[$winner]++;
}

echo array_search(max($winners), $winners)."\n";
