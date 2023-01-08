<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);

$input = $ir->lines();

$p1 = $p2 = false;

$snafu2dec = [
    "=" => -2,
    "-" => -1,
    "0" => 0,
    "1" => 1,
    "2" => 2,
];
$dec2snafu = array_flip($snafu2dec);

$nums = [];
foreach ($input as $k => $line) {
    $chars = array_reverse(str_split($line));
    $num = 0;
    $i = 0;
    foreach ($chars as $c) {
        $val = $snafu2dec[$c];
        $num += pow(5,$i)*$val;
        $i++;
    }
    $nums[] = $num;
}

$p1 = snafu(array_sum($nums), $dec2snafu);
echo "P1: $p1\n";

function snafu($num, $dec2snafu) {
    $i = 1;
    $snafu = [];
    while ($num > 0) {
        $rem = ($num % pow(5, $i));
        $num -= $rem;
        $rem = $rem / pow(5, $i-1);
        $snafu[$i] = ($snafu[$i] ?? 0) + $rem;
        while ($snafu[$i] >= 3) {
            $snafu[$i+1] = ($snafu[$i+1] ?? 0) + 1;
            $snafu[$i] -= 5;
        }

        $i++;
    }

    while(isset($snafu[$i]) && $snafu[$i] >= 3) {
        while ($snafu[$i] >= 3) {
            $snafu[$i+1] = ($snafu[$i+1] ?? 0) + 1;
            $snafu[$i] -= 5;
        }
        $i++;
    }
    $ret = "";
    foreach ($snafu as $c) {
        $ret = $dec2snafu[$c] . $ret;
    }

    return $ret;
}
