<?php

$test = false;

$file = ($test) ? "test" : "input";
$input = explode("\n", trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file)));
$input = array_filter($input);
#$input = str_split($input[0]);

$regex = "//";
$values = [];
$outString = "";
$p1 = 0;
$p2 = 0;

foreach ($input as $k => $line) {
    $p = array_map("intval", preg_split("/\s+/", $line));
    $p1 += max($p) - min($p);

    foreach ($p as $k) {
        foreach ($p as $l) {
            if ($k == $l) {
                continue;
            }
            if (($k / $l) === (int)($k / $l)) {
                #echo "$k::$l\n";
                $p2 += $k / $l;
                break 2;
            } else if (($l / $k) === (int)($l / $k)) {
                #echo "$k::$l\n";
                $p2 += $l / $k;
                break 2;
            }
        }
    }
}

echo "Part 1: " . $p1 . "\n";
echo "Part 2: " . $p2 . "\n";
#echo $outString;
