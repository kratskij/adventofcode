<?php

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->lines();

$tzAdjust = [
    "America/Mazatlan" => 3600,
    "America/Mexico_City" => 3600,
    "Pacific/Easter" => 0, // ?
    "America/Santiago" => 0, // ?
    "Africa/Casablanca" => -3600,
    "Africa/Sao_Tome" => 0, // ?
    "Africa/Juba" =>-3600,
    "Asia/Hebron" => -3600,
    "Europe/Volgograd" => 0, // ?
    "Asia/Tehran" => -3600,
    "Asia/Qyzylorda" =>-3600 ,
    "Antarctica/Vostok" => -3600,
    "Asia/Pyongyang" => 1800,
    "Antarctica/Casey" => -10800,
];

$counts = [];
foreach ($input as $k => $line) {
    [$time, $tz] = explode("; ", $line);
    $time = date_create_from_format("Y-m-d H:i:s", $time, new DateTimeZone($tz))->getTimestamp();

    $realTimestamp = $time;
    if (isset($tzAdjust[$tz])) {
        $realTimestamp += $tzAdjust[$tz];
    }
    if (!isset($counts[$realTimestamp])) {
        $counts[$realTimestamp] = 0;
    }
    $counts[$realTimestamp]++;

    if ($time != $realTimestamp) {
        // there are so many bugs, so adding the original time seems to make it more correct ...
        if (!isset($counts[$time])) {
            $counts[$time] = 0;
        }
        $counts[$time]++;
    }
}

$max = max($counts);

$f = [];
foreach ($counts as $t => $count) {
    if ($count == $max) {
        $f[$t] = $count;
    }
}

foreach ($f as $f2 => $count) {
    $ans = date_create_from_format("U", $f2)->format(DateTime::ATOM);
}

echo "Answer: $ans\n";
