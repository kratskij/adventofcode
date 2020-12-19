<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->lines();

const ADDITION = "+";
const MULTIPLICATION = "*";

$p1 = solve($input);
$p2 = solve($input, true);

echo "P1: $p1\nP2: $p2\n";

function solve($input, $additionPrecedence = false) {
    $sums = [];
    foreach ($input as $k => $origLine) {
        $line = $origLine;
        preg_match_all("(\d+|\+|\*|\(|\))", $line, $parts);
        $parts = $parts[0];
        while (true) {
            $levels = 0;
            $level = 0;

            foreach ($parts as $part) {
                if ($part == "(") {
                    $level++;
                    $levels = max($levels, $level);
                } else if ($part == ")") {
                    $level--;
                }
            }
            if ($levels == 0) {
                break;
            }

            $newParts = [];

            $evalParts = [];
            foreach ($parts as $part) {
                if ($part == "(") {
                    $level++;
                    if ($level == $levels) {
                        $evalParts = [];
                    }
                } else if ($part == ")") {
                    if ($level == $levels) {
                        $part = evaluate($evalParts, $additionPrecedence);
                    }
                    $level--;
                }
                if ($level == $levels) {
                    if ($part != "(") {
                        $evalParts[] = $part;
                    }
                } else {
                    $newParts[] = $part;
                }
            }
            $parts = $newParts;
        }
        $sums[] = evaluate($parts, $additionPrecedence);
    }
    return array_sum($sums);
}

function evaluate($parts, $additionPrecedence) {
    while ($additionPrecedence && ($pos = array_search("+", $parts)) !== false) {
        $parts = array_merge(
            array_slice($parts, 0, $pos - 1),
            [$parts[$pos - 1] + $parts[$pos + 1]],
            array_slice($parts, $pos + 2)
        );
    }

    $operator = ADDITION;
    $sum = 0;

    foreach ($parts as $next) {
        if ($next == ADDITION || $next == MULTIPLICATION) {
            $operator = $next;
        } else {
            switch ($operator) {
                case ADDITION:
                    $sum += $next;
                    break;
                case MULTIPLICATION:
                    $sum *= $next;
                    break;
            }
        }
    }

    return $sum;
}
