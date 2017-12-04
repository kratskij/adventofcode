<?php

$test = false;

$file = ($test) ? "test" : "input";
$input = explode("\n", trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file)));

$sum =[
    1 => 0,
    2 => 0
];

foreach ($input as $k => $line) {
    $words = preg_split("/\s+/", $line);
    if (count($words) == count(array_unique($words))) {
        $sum[1]++;
    }
    foreach ($words as &$word) {
        $word = str_split($word);
        sort($word);
        $word = implode("", $word);
    }
    if (count($words) == count(array_unique($words))) {
        $sum[2]++;
    }
}

echo "Part 1: " . $sum[1] . "\n";
echo "Part 2: " . $sum[2] . "\n";

#echo $sum;
#echo $outString;
