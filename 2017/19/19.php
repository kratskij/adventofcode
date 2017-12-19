<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";

$file = ($test) ? "test" : "input";
$input = explode("\n", file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file));

$grid = [];
foreach ($input as $k => $line) {
    $grid[] = str_split($line);
}

$y = 0;
$x = array_search("|", $grid[0]);
$dir = "d";

$letters = [];
$count = 0;
while (true) {
    $char = $grid[$y][$x];
    switch ($char) {
        case "+":
            if (in_array($dir, ["u", "d"])) {
                $dir =$grid[$y][$x+1] != " " ? "r" : "l";
            } else {
                $dir = $grid[$y-1][$x] != " " ? "u" : "d";
            }
            break;
        case "-":
        case "|":
            break;
        case " ":
            echo "Part 1: " . implode("", $letters) . "\n";
            echo "Part 2: $count\n";
            die();
        default:
            $letters[] = $char;

    }
    move($dir, $x, $y);
        $count++;
}


function isBlank($c) {
    return ($c == " ");
}

function move($dir, &$x, &$y) {
    if ($dir == "u") $y--;
    else if ($dir == "d") $y++;
    else if ($dir == "l") $x--;
    else if ($dir == "r") $x++;
}
#echo $sum;
#echo $outString;
