<?php

require_once(__DIR__."/../inputReader.php");

$file = $argv[1] ?? "input";
$test = $file == "test";

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->explode("\n\n");
$ans = 0;
foreach ($input as $k => $line) {
    [$from, $to] = explode("\n", $line);
    $ans += (getUts($to) - getUts($from)) / 60;
}

echo "Answer: $ans\n";

function getUts($line) {
    preg_match("/^\w+\:\s+([a-zA-Z\/_\-]+)\s+(.*)$/", $line, $m);
    return (new \DateTime($m[2], new \DateTimeZone($m[1])))->format("U");
}
