<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");
require_once __DIR__."/../Util.php";

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);

$input = $ir->lines();

$ans = 0;

foreach ($input as $k => $line) {
    $len = mb_strlen($line);
    if ($len < 4 || $len > 12) {
        continue;
    }
    $line = Util::removeAccents($line);

    if (
        mb_ereg('\d', $line) === false ||
        mb_ereg('[aeiou]', strtolower($line)) === false ||
        mb_ereg('[bcdfghjklmnpqrstvwxyz]', strtolower($line)) === false ||
        count(array_unique(mb_str_split(strtolower($line)))) !== $len
    ) {
        continue;
    }

    $ans++;
}
echo "Answer: $ans\n";
