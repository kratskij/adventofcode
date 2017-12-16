<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";

$file = ($test) ? "test" : "input";
$input = explode(",", trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file)));

$programs = ($test) ? "abcde" : "abcdefghijklmnop";

$initValue = $programs;
$hashes = [];
$count = 0;
$first = true;

while (true) {
    foreach ($input as $k => $line) {
        if ($programs == $initValue && $k == 0 && !$first) {
            echo "Part 2: " . substr(array_search(1000000000 % $count, $hashes), 0, 16) . "\n";
            break 2;
        }

        $hashes[$programs.$k] = $count;
        $count++;
        $rest = substr($line, 1);

        switch (substr($line, 0, 1)) {
            case "s":
                $programs = substr(substr($programs, -$rest) . $programs, 0, 16);
                break;
            case "p":
                list($a, $b) = explode("/", $rest);
                $programs = str_replace($a, "*", $programs);
                $programs = str_replace($b, $a, $programs);
                $programs = str_replace("*", $b, $programs);
                break;
            case "x":
                list($a, $b) = explode("/", $rest);
                $arr = str_split($programs);
                $help = $arr[$a];
                $arr[$a] = $arr[$b];
                $arr[$b] = $help;
                $programs = implode("", $arr);
                break;
        }
    }
    if ($first) {
        echo "Part 1: $programs\n";
        $first = false;
    }
}
