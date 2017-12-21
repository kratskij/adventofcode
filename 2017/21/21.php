<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";

$file = ($test) ? "test" : "input";
$input = explode("\n", trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file)));
#$input = str_split($input[0]);
#$input = preg_match ("//" , $input, &$matches)
#var_dump($input);

$count = ($test) ? 2 : 5;
$regex = "//";
$values = [];
$outString = "";
$sum = 0;
$c = 0;

$image = ".#./..#/###";
$rules = [];
foreach ($input as $k => $line) {
    $p = explode(" => ", $line);
    $rules[$p[0]] = $p[1];
}

function splitAndCombineImage($image, $rules)
{
    $exp = explode("/", $image);
    if ((count($exp) % 2) == 0) {
        $breakInto = 2;
    } else {
        $breakInto = 3;
    }
    $eachLen = count($exp) / $breakInto;
    $img = explode("/", $image);

    $subImgs = [];
    $newRows = [];

    for ($i = 0; $i < $eachLen; $i++) {
        for ($j = 0; $j < $eachLen; $j++) {
            $oldImg = implode("/", array_map(function($line) use ($j, $breakInto) { return substr($line, $j*$breakInto, $breakInto); }, array_slice($img, $i*$breakInto, $breakInto)));
            $newImg = isMatch($oldImg, $rules);
            if ($newImg) {
                $subImgs[$i][$j] = $newImg;
            } else {
                echo "no new image found\n";
            }
        }
    }

    foreach ($subImgs as $i => $row) {
        for ($x = 0; $x < count(explode("/", $row[0])); $x++) {
            $newRows[] = implode("", array_map(function($i) use ($x) { return explode("/", $i)[$x]; }, $row));
        }
    }
    $image = implode("/", $newRows);

    return $image;
}

function pr($img)
{
    echo "\n";
    foreach (explode("/", $img) as $l) {
        echo $l."\n";
    }
    echo "\n";
}

function isMatch($img, $rules)
{
    static $cache;
    if ($cache === null) {
        $cache = [];
    }
    if (isset($cache[$img])) {
        return $cache[$img];
    }
    foreach (rotateflip($img) as $i) {
        if (isset($rules[$i])) {
            $cache[$img] = $rules[$i];
            return $rules[$i];
        }
    }
    return false;
}

function rotate($img, $num = 1) {
    $rows = explode("/", $img);
    foreach ($rows as &$r) {
        $r = str_split($r);
    }
    $n = count($rows);

    for ($x = 0; $x < $num; $x++) {
        $rotated = [];
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $rotated[$i][$j] = $rows[$n-$j-1][$i];
            }
        }
        $rows = $rotated;
    }

    foreach ($rows as &$r) {
        $r = implode("", $r);
    }

    return implode("/", $rows);
}

function rotateflip($img)
{
    $return = [];

    $return[] = $img;
    $return[] = rotate($img);
    $return[] = rotate($img, 2);
    $return[] = rotate($img, 3);

    $img = implode("/", array_map(function($r) { return strrev($r); }, explode("/", $img)));

    $return[] = $img;
    $return[] = rotate($img);
    $return[] = rotate($img, 2);
    $return[] = rotate($img, 3);

    return array_unique($return);
}

$imageCp = $image;
for ($i = 0; $i < $count; $i++) {
    $imageCp = splitAndCombineImage($imageCp, $rules);
}
echo "Part 1: " . substr_count($imageCp, "#") . "\n";

$imageCp = $image;
for ($i = 0; $i < 18; $i++) {
    echo $i."\n";
    $imageCp = splitAndCombineImage($imageCp, $rules);
}
echo "Part 1: " . substr_count($imageCp, "#") . "\n";
