<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->lines();
$input = array_map("intval", $input);
$input[] = 0;
sort($input);

$prev = 0;
$ones = $threes = 0;
$connections = [];

$count = 0;
foreach ($input as $k => $i) {
    if ($i - $prev == 1) {
        $ones++;
    } else if ($i - $prev == 3) {
        $threes++;
    }
    $next = $k + 1;
    while (isset($input[$next]) && $input[$next] <= $i + 3) {
        $connections[$i][$input[$next]] = $input[$next];
        $next++;
    }
    $prev = $i;
}

$p1 = $ones * ($threes + 1);
$p2 = countChildren($connections, 0);

echo "P1: $p1\nP2: $p2\n";

function countChildren(&$connections, $node) {
    static $cache;
    if (is_null($cache)) {
        $cache = [];
    }
    if (!isset($cache[$node])) {
        $x = 0;
        if (!isset($connections[$node])) {
            return 1;
        }

        foreach ($connections[$node] as $new) {
            $x += countChildren($connections, $new);
        }
        $cache[$node] = $x;
    }

    return $cache[$node];
}
