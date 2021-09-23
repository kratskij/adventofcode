<?php

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->regex("(.*) \[(.*)\]");

$max = $maxLine = 0;
foreach ($input as $k => $line) {
    list($letters, $names) = $line;
    $names = explode(", ", strtolower($names));

    $namesFound = 0;
    foreach ($names as $name) {
        $regex = implode(".*", str_split($name));
        if (preg_match("/$regex/i", $letters)) {
            $pos = 0;
            while ($name) {
                $pos = strpos($letters, $name[0], $pos);
                $name = substr($name, 1);
                $letters = substr($letters, 0, $pos) . substr($letters, $pos + 1);
            }
            $namesFound++;
        }
    }
    if ($namesFound > $max) {
        $max = $namesFound;
        $maxLine = $k;
    }
}

echo "$maxLine\n";
#688
