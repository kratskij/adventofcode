<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->lines();

foreach ($input as $row => $origLine) {
    $candidates = [$origLine];
    while (strlen(end($candidates)) > 1) {
        $line = substr(end($candidates), 1);
        foreach (str_split($line) as $k => $char) {
            $line[$k] = chr((((ord($char) + 1 + ord(end($candidates)[$k]) - 97) - 97) % 26) + 97);
        }
        $candidates[] = $line;
    }


    foreach (str_split($origLine) as $x => $char) {
        $pw = "";
        foreach ($candidates as $y => $word) {
            if (isset($candidates[$y][$x])) {
                $pw .= $candidates[$y][$x];
            }
        }

        if (strpos($pw, "eamqia") !== false) {
            echo $origLine."\n";
        }
    }
}
