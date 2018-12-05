<?php

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file);
$input = $ir->raw();

$uniqChars = array_unique(str_split(strtolower($input)));

$p1 = strlen(polymer($input, $uniqChars));
$p2 = min(array_map(
    function($char) use ($input, $uniqChars) {
        $x = str_replace($char, "", $input);
        $x = str_replace(strtoupper($char), "", $x);

        #echo $input."\n";
        return strlen(polymer($x, $uniqChars));
    },
    $uniqChars
));

echo "Part 1: $p1\n";
echo "Part 2: $p2\n";

function polymer($input, $uniqChars) {
    $prevInput = $input;
    while (true) {
        foreach ($uniqChars as $char) {
            $input = str_replace(strtoupper($char).$char, "", $input);
            $input = str_replace($char.strtoupper($char), "", $input);
        }
        if ($prevInput == $input) {
            break;
        }
        $prevInput = $input;
    }
    return $input;
}
