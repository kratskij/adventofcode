<?php

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);

$input = $ir->lines();
$ans = 0;

foreach ($input as $k => $line) {
    $embedding = 0;
    $chars = mb_str_split($line);
    $i = 0;
    $g = [];
    foreach ($chars as $j => $char) {
        switch (mb_ord($char)) {
            case 8295; #u2067
            case 8294; #u2066
                $embedding++;
                break;
            case 8297; #u2069
                $embedding--;
                break;
            default:
                $tmpEmbedding = $embedding;
                if (is_numeric($char) && isset($chars[$j-1]) && is_numeric($chars[$j-1])) {
                    // use same index as previous char
                    $i--;
                }
                if (!isset($g[$i])) {
                    $g[$i] = [
                        "embedding" => $tmpEmbedding,
                        "char" => ""
                    ];
                }
                $g[$i]["char"] .= $char;
                $i++;
        }
    }

    $prevWasMax = false;
    $orig = implode("", array_column($g, "char"));
    $max = max(array_column($g, "embedding"));
    while ($max > 0) {
        foreach ($g as $i => $v) {
            if ($v["embedding"] == $max) {
                if (!$prevWasMax) {
                    $start = $i;
                }
                $prevWasMax = true;
            }
            if ($v["embedding"] < $max || !isset($g[$i+1])) {
                if ($prevWasMax) {
                    $end = ($v["embedding"] < $max) ? $i-1 : $i;
                    for ($d = 0; $d <= ($end - $start) / 2; $d++) {
                        $tmp = $g[$start + $d];
                        $tmp2 = $g[$end - $d];

                        $tmp["char"] = ($tmp["char"] == "(") ? ")" : ($tmp["char"] == ")" ? "(" : $tmp["char"]);
                        $tmp2["char"] = ($tmp2["char"] == "(") ? ")" : ($tmp2["char"] == ")" ? "(" : $tmp2["char"]);

                        $tmp["embedding"]--;
                        $tmp2["embedding"]--;
                        $g[$end - $d] = $tmp;
                        $g[$start + $d] = $tmp2;
                    }
                }
                $start = false;
                $prevWasMax = false;
            }
        }
        $max--;
    }
    $fixed = implode("", array_column($g, "char"));

    eval("\$origValue = $orig;");
    eval("\$fixedValue = $fixed;");
    $ans += abs($origValue - $fixedValue);
}

echo "Answer: $ans\n";
