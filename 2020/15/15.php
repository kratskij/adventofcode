<?php

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$s = $ir->explode(",");
$s = array_map("intval", $s);

$prev = $prev2 = [];
$i = 1;
foreach ($s as $number) {
    if (isset($prev[$number])) {
        $prev2[$number] = $prev[$number];
    }
    $prev[$number] = $i++;
}

$stopAt = [2020 => "P1", 30000000 => "P2"];
$found = [];
while (true) {
    $number = isset($prev2[$number]) ? $prev[$number] - $prev2[$number] : 0;

    if (isset($stopAt[$i])) {
        $found[$i] = true;
        echo "{$stopAt[$i]}: $number\n";
        if (count($found) == count($stopAt)) {
            break;
        }
    }

    if (isset($prev[$number])) {
        $prev2[$number] = $prev[$number];
    }
    $prev[$number] = $i++;

}
