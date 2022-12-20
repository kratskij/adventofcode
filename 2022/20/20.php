<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");


$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->lines();
$input = array_map("intval", $input);
$list = [];

foreach ($input as $i => $val) {
    $list[$i] = [
        "visited" => 0,
        "value" => $val,
        "origI" => $i,
    ];
}
$p1 = run($list, 1);
echo "P1: $p1\n";

$decryptKey = 811589153;
foreach ($list as &$l) {
    $l["value"] *= $decryptKey;
}

$p2 = run($list, 10);

echo "P2: $p2\n";

function run($list, $repeats) {
    $l = count($list);
    $origList = $list;
    for ($times = 1; $times <= $repeats; $times++) {
        $i = 0;
        while ($i < $l) {
            $val = $origList[$i]["value"];
            $found = false;
            foreach ($list as $j => $el) {
                if ($el["origI"] == $i && $el["visited"] < $times) {
                    $found = true;
                    unset($list[$j]);

                    $moveTo = $j + $el["value"];
                    $moveTo = ($moveTo + $l - 1) % ($l - 1);
                    if ($moveTo == 0) {
                        $moveTo = $l-1;
                    }

                    $before = array_slice($list, 0, $moveTo);
                    $after = array_slice($list, $moveTo);

                    $el["visited"]++;
                    $list = array_merge($before, [$el], $after);
                    $list = array_values($list);

                    break;
                }
            }
            if (!$found) {
                $i++;
            }
        }
    }
    foreach ($list as $i => $el) {
        if ($el["value"] === 0) {
            $nullAt = $i;
            break;
        }
    }

    return $list[($nullAt+1000)%$l]["value"] + $list[($nullAt+2000)%$l]["value"] + $list[($nullAt+3000)%$l]["value"];
}
