<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim();
#$input = $ir->raw();
$input = $ir->lines();
#$input = $ir->chars();
#$input = $ir->csv("\t");
#$input = $ir->explode(",");
#$input = $ir->regex(",");

// Cast to int
#$input = array_map("intval", $input);

$val = false;

foreach ($input as $k => $i) {
    #list($width, $height) = $i;
}
echo $val;
