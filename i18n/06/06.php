<?php

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim();
$input = $ir->lines();
$words = [];
$k = 0;
while ($line = array_shift($input)) {
    if ($line == "") {
        break;
    }
    if ((($k % 3) == 2)) {
        $line = iconv('UTF-8', 'ISO-8859-1', $line);
    }
    if (($k % 5) == 4) {
        $line = iconv('UTF-8', 'ISO-8859-1', $line);
    }

    $words[] = $line;
    $k++;
}

$ans = 0;
while ($line = array_shift($input)) {
    $regex = trim($line);
    foreach ($words as $word) {
        if (mb_ereg("^$regex$", $word)) {
            $ans += array_search($word, $words) + 1;
            break;
        }
    }
}

echo "Answer: $ans\n";
