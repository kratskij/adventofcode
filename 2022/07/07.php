<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->lines();

$dirTree = commands2directoryTree($input);
$dirSizes = [];
$totalSize = dirSize($dirTree, "", $dirSizes);
$missing = 30000000-(70000000-$totalSize);

$p1 = array_sum(
    array_filter(
        $dirSizes,
        function($size) {
            return $size <= 100000;
        }
    )
);
$p2 = min(
    array_filter(
        $dirSizes,
        function($size) use ($missing) {
            return $size >= $missing;
        }
    )
);

echo "P1: $p1\nP2: $p2\n";

function commands2directoryTree($consoleLog) {
    $paths = [];
    $currentPath = [];

    foreach ($consoleLog as $k => $line) {
        $words = explode(" ", $line);
        $first = array_shift($words);
        if ($first == "$") {
            $command = array_shift($words);
            if ($command == "cd") {
                $args = implode(" ", $words);
                if ($args == "/") {
                    $currentPath = [];
                } else if ($args == "..") {
                    array_pop($currentPath);
                } else {
                    $currentPath[] = $args;
                }
            }
        } else if ($first == "dir") {
            $dir = array_shift($words);
            $idx = implode("/", $currentPath) . "/" . $dir;
            if (!isset($paths[$idx])) {
                $paths[$idx] = [];
            }
        } else if (is_numeric($first)) {
            $file = array_shift($words);
            $paths[implode("/", $currentPath) . "/" . $file] = (int)$first;
        }
    }
    ksort($paths);

    $dirTree = [];
    foreach ($paths as $key => $value) {
        $ref = &$dirTree;
        $dirs = explode("/", $key);
        $last = max(array_keys($dirs));

        $prefix = "";
        foreach ($dirs as $k => $dir) {
            $prefix .= "\t";
            if ($k == $last && is_numeric($value)) {
                $ref[$dir] = $value;
                break;
            } else if (!isset($ref[$dir])) {
                $ref[$dir] = [];
            }
            $ref = &$ref[$dir];
        }
    }
    unset($ref);

    return $dirTree;
}

function dirSize($struct, $path = "", &$dirSizes) {
    $size = 0;
    foreach ($struct as $k => $s) {
        if (is_numeric($s)) {
            $size += $s;
        } else {
            $size += dirSize($s, $path."/".$k, $dirSizes);
        }
    }
    $dirSizes[$path] = $size;

    return $size;
}
