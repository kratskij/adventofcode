<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);

$drawn = ($test)
    ? [7,4,9,5,11,17,23,2,0,14,21,24,10,16,13,6,15,25,12,22,18,20,8,19,3,26,1]
    : [57,9,8,30,40,62,24,70,54,73,12,3,71,95,58,88,23,81,53,80,22,45,98,37,18,72,14,20,66,0,19,31,82,34,55,29,27,96,48,28,87,83,36,26,63,21,5,46,33,86,32,56,6,38,52,16,41,74,99,77,13,35,65,4,78,91,90,43,1,2,64,60,94,85,61,84,42,76,68,10,49,89,11,17,79,69,39,50,25,51,47,93,44,92,59,75,7,97,67,15]
;

$input = $ir->csv(" ");

$p1 = $p2 = false;

$boards = createBoards($input);

foreach ($drawn as $dNum) {
    markBoards($boards, $dNum);
    $winners = check($boards);
    foreach ($winners as $winnerId) {
        $winner = $boards[$winnerId];
        if (!$p1) {
            $p1 = array_sum(array_map("array_sum", $winner)) * $dNum;
        }
        $p2 = array_sum(array_map("array_sum", $winner)) * $dNum;
        unset($boards[$winnerId]);
    }
}

echo "P1: $p1\nP2: $p2\n";

function createBoards($input) {
    $boards = [];
    $id = $row = $col = 0;
    foreach ($input as $k => $line) {
        if (empty(array_filter($line))) {
            $row = 0;
            $id++;
        }
        $col = 0;
        foreach ($line as $num) {
            if ($num === "") { continue; }

            $boards[$id][$row][$col] = (int)$num;
            $col++;
        }
        $row++;
    }
    return $boards;
}

function markBoards(&$boards, $dNum) {
    foreach ($boards as $id => $board) {
        foreach ($board as $row => $line) {
            foreach ($line as $col => $num) {
                if ($num == $dNum) {
                    $boards[$id][$row][$col] = false;
                }
            }
        }
    }
}

function check($boards) {
    $completedBoards = [];
    foreach ($boards as $id => $board) {
        $remCols = $remRows = [];
        foreach ($board as $row => $line) {
            foreach ($line as $col => $num) {
                if ($boards[$id][$row][$col] !== false) {
                    $remCols[$col] = true;
                    $remRows[$row] = true;
                }
            }
        }
        if (count($remCols) !== 5 || count($remRows) !== 5) {
            $completedBoards[] = $id;
        }
    }
    return $completedBoards;
}
