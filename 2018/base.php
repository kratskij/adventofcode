<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file);
#$input = $ir->raw();
$input = $ir->lines();
#$input = $ir->chars();
#$input = $ir->csv("\t");
#$input = $ir->explode(",");
#list($id, $leftOffset, $topOffset, $width, $height) = $i;

// Cast to int
#$input = array_map("intval", $input);

$val = false;

foreach ($input as $k => $i) {
    #list($regexstuff) = $i;
}
echo $val;
