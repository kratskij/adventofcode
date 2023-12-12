<?php

ini_set('memory_limit','12048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");
#require_once __DIR__."/../Util.php";

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim();
$input = $ir->lines();

$p1 = $p2 = 0;

const DAM = ".";
const OP = "#";

foreach ($input as $k => $line) {
    [$springs, $sums] = explode(" ", $line);
    $springs = $springs."?".$springs."?".$springs."?".$springs."?".$springs;
    $sums = $sums.",".$sums.",".$sums.",".$sums.",".$sums;
    $split = explode(",", $sums);
    $variants = [""];
    $l = strlen($springs);
    for ($i = 0; $i < $l; $i++) {
        $new = [];
        foreach ($variants as $v) {
            if ($springs[$i] == OP) {
                if (validate($v . OP, $sums, $split)) {
                    $new[] = $v . OP;
                }
            } else if ($springs[$i] == DAM) {
                if (validate($v . DAM, $sums, $split)) {
                    $new[] = $v . DAM;
                }
            } else {
                if (validate($v . OP, $sums, $split)) {
                    $new[] = $v . OP;
                }
                if (validate($v . DAM, $sums, $split)) {
                    $new[] = $v . DAM;
                }
            }
        }
        $variants = $new;
    }
echo count($variants)."\n";
    $k = 0;
    #echo $line."\n";
    foreach ($variants as $var) {
        #echo "\t: $var\n";
        $parts = array_filter(preg_split("/[^#]+/", $var));
        if (implode(",", array_map("strlen", $parts)) == $sums) {
            $k++;
        } else {
            echo "wtf: $var ($sums)\n";
        }
    }
    echo "\t$line: $k\n";
    $p1 += $k;
    #echo "\t$k\n";
}

function validate($str, $sums, $split) {
    $parts = preg_split("/[^#]+/", $str);
    $p = array_map("strlen", array_filter($parts));
    $n = array_pop($p);
    $exp = implode(",", $p);
    $l = count($p);
    if ($exp != substr($sums, 0, strlen($exp)) || $l + 1 > count($split) || $n > ($split[$l])) {
        #echo "\t$v: exp: '$exp', " . substr($sums, 0, strlen($exp)) . ", " . implode(",", array_map("strlen", array_filter($parts))) . "\n";
        return false;
    }
    return true;
}
echo "P1: $p1\nP2: $p2\n";
