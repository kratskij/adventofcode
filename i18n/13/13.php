<?php

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->lines();

$encodings = ["UTF-16LE", "UTF-16BE", "UTF-8", "latin1"];

$words = [];
while ($line = array_shift($input)) {
    $packed = pack("H*", $line);

    foreach ($encodings as $enc) {
        if ($r = @iconv($enc, "ASCII//TRANSLIT", $packed)) {
            if ($r != str_repeat("?", mb_strlen($r))) {
                $words[] = $r;
                break;
            }
        }
    }
}

$ans = 0;
while ($line = array_shift($input)) {
    $regex = trim($line);
    $matches = [];
    foreach ($words as $word) {
        if (mb_ereg("^$regex$", $word)) {
            $matches[$word] = array_search($word, $words) + 1;
        }
    }
    if (count($matches) > 1) {
        $matches = array_filter($matches, function($v, $k) {
            return !strpos($k, "ss");
        }, ARRAY_FILTER_USE_BOTH);
    }

    $ans += reset($matches);
}

echo "Answer: $ans\n";
