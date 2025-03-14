<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");
#require_once __DIR__."/../Util.php";

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim();

$input = $ir->lines();
#$input = $ir->raw();
#$input = $ir->chars();
#$input = $ir->csv("\t");
#$input = $ir->explode(",");
#$input = $ir->regex(",");
#$input = $ir->extractNumbers();

#$grid = $ir->grid(["#" => true]);
#$grid = $ir->intGrid();
/*foreach ($grid as $y => $row) {
    foreach ($row as $x => $val) {

    }
}*/

$ans = 0;

foreach ($input as $k => $line) {

}
echo "Answer: $ans\n";
