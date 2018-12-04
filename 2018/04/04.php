<?php

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file);
$input = $ir->regex("\[(\d+)\-(\d+)\-(\d+)\s(\d+)\:(\d+)\]\s(.*)");
sort($input);

$currentGuardId = false;
$sleptFrom = 0;
$sleeps = [];

foreach ($input as $k => $line) {
    list($y, $month, $d, $h, $m, $msg) = $line;
    $y += 500;
    $time = strtotime("$y-$month-$d $h:$m");
    $parts = explode(" ", trim($msg));
    switch ($parts[0]) {
        case "Guard":
            $currentGuardId = trim($parts[1], "#");
            break;
        case "falls":
            $sleptFrom = $time;
            break;
        case "wakes":
            for ($i=$sleptFrom; $i<$time; $i += 60) {
                @$sleeps[$currentGuardId][date("i", $i)]++;
            }
            break;
        default:
            throw new Exception("WUT");
    }
}


$superSleeper = [0, null];
$topGuardMinute = [0, null];

foreach ($sleeps as $guardId => $minutes) {
    $totalMinutes = array_sum($minutes);
    $topMinute = max($minutes);
    $topMinuteValue = array_search($topMinute, $minutes);

    if ($totalMinutes > $superSleeper[0]) {
        $superSleeper = [$totalMinutes, $guardId * $topMinuteValue];
    }
    if ($topMinute > $topGuardMinute[0]) {
        $topGuardMinute = [$topMinute, $guardId * $topMinuteValue];
    }
}


echo "Part 1: " . $superSleeper[1] . "\n";
echo "Part 2: " . $topGuardMinute[1] . "\n";
