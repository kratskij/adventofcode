<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim();
#$input = $ir->raw();
$input = $ir->lines();
#$input = $ir->chars();
#$input = $ir->csv("\t");
#$input = $ir->explode(",");
#$input = $ir->regex(",");
#$grid = $ir->grid(["#" => true]);
#foreach ($grid as $y => $row) {
#    foreach ($row as $x => $val) {
     #}
#}

// Cast to int
#$input = array_map("intval", $input);

$p1 = $p2 = false;

foreach ($input as $k => $line) {
    #list($width, $height) = $line;
}
echo "P1: $p1\nP2: $p2\n";
