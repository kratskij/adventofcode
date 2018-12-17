<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->regex("^(\w)\=([\d\-]+)\,\s(\w)\=([\d\-]+)\.\.([\d\-]+)$");

// Cast to int
#$input = array_map("intval", $input);

$val = false;
$grid = [];

foreach ($input as $k => $line) {
    list($y, $val1, $x, $from, $to) = $line;
    for ($i = $from; $i <= $to; $i++) {
        if ($y == "x") {
            $grid[$i][$val1] = "#";
        } else if ($y == "y") {
            $grid[$val1][$i] = "#";
        }
    }
}
printGrid($grid);
#var_Dump($grid);

$new = true;
define("FALLING", 1);
define("SEARCH_LEFT", 2);
define("SEARCH_RIGHT", 3);

$fillers = [
    ["y" => 0, "x" => 500, "mode" => FALLING]
];
$maxY = max(array_keys($grid));
$filled = [];

while ($fillers) {
    $newFillers = [];
    foreach ($fillers as &$filler) {
        move($grid, $filler["y"], $filler["x"], $filler["mode"], $newFillers, $filled);
        fill($grid, $filler["y"], $filler["x"], $filler["mode"], false, $filled);
        if (!isset($grid[$filler["y"]+1][$filler["x"]])) {
            $filler["mode"] = FALLING;
        } else if (isset($grid[$filler["y"]+1][$filler["x"]]) && $grid[$filler["y"]+1][$filler["x"]] == "#") {
            fill($grid, $filler["y"]+1, $filler["x"], $filler["mode"], true, $filled);
        }
    }
    $fillers = array_merge($fillers, $newFillers);
    $fillers = array_filter($fillers, function($filler) use ($maxY) {
        return $filler["y"] <= $maxY;
    });
    $uniqueFillers = [];
    foreach ($fillers as $f) {
        $uniqueFillers[$f["y"] . "_" . $f["x"] . "_" . $f["mode"]] = $f;
    }
    $fillers = $uniqueFillers;
    #printGrid($grid, $fillers, $filled);

    echo "Filled: " . count($filled) . "\n";




}
printGrid($grid, [], $filled);

function fill(&$grid, $y, $x, $mode, $clay = false, &$filled) {
    if (!$clay) {
        if (($mode == FALLING)) {
            $grid[$y][$x] = "|";
        } else {
            $grid[$y][$x] = "~";
            $filled[$y."_".$x] = true;
        }
    } else if ($grid[$y][$x] == "#"){
        $filled[$y."_".$x] = true;
    } else {
        die('NO');
    }
}

function move($grid, &$y, &$x, &$mode, &$newFillers, &$filled) {
    switch ($mode) {
        case FALLING:
            $y++;
            if (isset($grid[$y][$x])) {
                if ($grid[$y][$x] == "#") {
                    $y--;
                    $mode = SEARCH_LEFT;
                    $newFillers[] = ["y" => $y, "x" => $x, "mode" => SEARCH_RIGHT];
                    return;
                } elseif ($grid[$y][$x] == "|") {
                    return;
                }
                move($grid, $y, $x, $mode, $newFillers, $filled);
            }
            break;
        case SEARCH_LEFT:
            $x--;
            if (isset($grid[$y][$x])) {
                if ($grid[$y][$x] == "#") {
                    fill($grid, $y, $x, $mode, true, $filled);
                    #$y--;
                    $x++;
                    $mode = SEARCH_RIGHT;
                    return;
                } elseif ($grid[$y][$x] == "|") {
                    return;
                } elseif ($grid[$y][$x] == "~") {
                    //visited by another filler!
                    $mode = SEARCH_RIGHT;
                }
                move($grid, $y, $x, $mode, $newFillers, $filled);
            }
            break;
        case SEARCH_RIGHT:
            $x++;
            if (isset($grid[$y][$x])) {
                if ($grid[$y][$x] == "#") {
                    fill($grid, $y, $x, $mode, true, $filled);
                    $y--;
                    $x--;
                    $mode = SEARCH_LEFT;
                    return;
                } elseif ($grid[$y][$x] == "|") {
                    return;
                }
                move($grid, $y, $x, $mode, $newFillers, $filled);
            }
            break;
    }
}

function printGrid($grid, $fillers, $filled) {
    var_Dump($filled);
    $maxX = -INF;
    $minX = INF;
    foreach ($grid as $g) {
        $maxX = max($maxX, max(array_keys($g)));
        $minX = min($minX, min(array_keys($g)));
    }
    var_dump($minX, $maxX);
    #die();
    $xLines = [0 => "", 1 => "", 2 => ""];
    for ($x = $minX; $x <= $maxX; $x++) {
        $xLines[0] .= substr($x, 0, 1);
        $xLines[1] .= substr($x, 1, 1);
        $xLines[2] .= substr($x, 2, 1);
    }
    foreach ($xLines as $line) {
        echo "     $line\n";
    }
    for ($y = 0; $y <= max(array_keys($grid)); $y++) {
        echo str_pad($y, 5, " ", STR_PAD_RIGHT);
        for ($x = $minX; $x <= $maxX; $x++) {
            if (isset($filled[$y."_".$x])) {
                echo "\033[1;32m";
            }
            if (isset($fillers[$y . "_" . $x . "_1"])) {
                echo "▼";
            } elseif (isset($fillers[$y . "_" . $x . "_2"])) {
                echo "◀";
            } elseif (isset($fillers[$y . "_" . $x . "_3"])) {
                echo "▶";
            } else {
                echo isset($grid[$y][$x]) ? $grid[$y][$x] : ".";
            }
            if (isset($filled[$y."_".$x])) {
                echo "\033[0m";
            }
        }
        echo "\n";
    }
    echo "\n";
}
