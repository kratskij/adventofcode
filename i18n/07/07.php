<?php

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->regex("^(.*)\s+(\d+)\s+(\d+)$");

$ans = 0;

$halifaxTz = new \DateTimeZone("America/Halifax");
$santiagoTz = new \DateTimeZone("America/Santiago");

foreach ($input as $k => $line) {
    [$time, $addMinutes, $subMinutes] = $line;
    $date = new \DateTime($time);
    $interval = \DateInterval::createFromDateString(($addMinutes - $subMinutes) . "minutes");

    $expectedFormat = $date->format(\DateTime::ATOM);

    if ($date->setTimezone($halifaxTz)->format(\DateTime::ATOM) == $expectedFormat) {
    } else if ($date->setTimezone($santiagoTz)->format(\DateTime::ATOM) == $expectedFormat) {
    } else {
        die('No match');
    }

    $oldOffset = $date->getOffset();
    $newOffset = $date->add($interval)->getOffset();

    if ($newOffset != $oldOffset) {
        // let's fix the DST bug in the report
        $date->add(\DateInterval::createFromDateString(($newOffset - $oldOffset) . " seconds"));
    }

    $ans += $date->format("H") * ($k + 1);
}

echo "Answer: $ans\n";
