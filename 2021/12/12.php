<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim();
$input = $ir->lines();

// Cast to int
#$input = array_map("intval", $input);

$p1 = $p2 = 0;


$a = [];
$r = [];
foreach ($input as $k => $line) {
    list($s, $e) = explode("-", $line);
    $a[$s][$e] = true;
    $a[$e][$s] = true;
}

$q = [ ["start", [], "", false] ];
$seen = [];
while ($cur = array_shift($q)) {
    if ((count($q) % 100) == 0) {
        echo count($q)."\n";
    }
    list($c, $v, $r, $smalltwice) = $cur;
    $v[$c] = ($v[$c] ?? 0) + 1;
    $r .= "-$c";
    if (($c === "start" && $v[$c] > 1)) {
        continue;
    }
    if ($smalltwice && strtolower($c) === $c && $v[$c] > 1) {
        continue;
    }

    if (strtolower($c) === $c && $v[$c] > 1) {
        $smalltwice = true;
    }

    if (isset($seen[$r])) {
        continue;
    }
    $seen[$r] = true;

    if ($c != "end") {
        if (isset($a[$c])) {
            foreach (array_keys($a[$c]) as $c) {
                $q[] = [$c, $v, $r, $smalltwice];
            }
        }
    } else {
        $p2++;
    }
}

echo "P1: $p1\nP2: $p2\n";
