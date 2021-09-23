<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file));
$grid = array_map("array_filter", $ir->grid(["#" => true, " " => false]));
$maxHeight = max(array_keys($grid)) - 1;
$trunks = $grid[$maxHeight];

$sellable = 0;
foreach ($trunks as $center => $none) {
    $symmetrical = true;
    $maxOffset = 0;
    for ($y = $maxHeight; $y >= 0; $y--) {
        $l = $r = true;
        $offset = 0;
        while ($l || $r || $offset <= $maxOffset) {
            if ($l ^ $r) {
                $symmetrical = false;
                break 2;
            }
            $maxOffset = max($offset, $maxOffset);
            $offset++;
            $l = isset($grid[$y][$center-$offset]);
            $r = isset($grid[$y][$center+$offset]);
        }
    }
    if ($symmetrical) {
        $sellable++;
    }
}
echo $sellable . "\n";

#5534: CORRECT
