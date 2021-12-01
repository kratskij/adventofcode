<?php

$sep = DIRECTORY_SEPARATOR;
$root = __DIR__ . $sep . "..";

$year = date("Y");
$day = date("d");

$baseDir = $root . $sep . $year;
$baseFile = $baseDir . $sep . "base.php";

if (!is_dir($baseDir)) {
    mkdir($baseDir);
    $irFile = $baseDir . $sep . "inputReader.php";
    $y = $year;
    while ($y--) {
        $prevBaseFile = $root . $sep . $y . $sep . "base.php";
        if (file_exists($prevBaseFile)) {
            copy($prevBaseFile, $baseFile);
            $prevIrFile = $root . $sep . $y . $sep . "inputReader.php";
            copy($prevIrFile, $irFile);
            break;
        }
    }
}


$todayDir = $baseDir . $sep . $day;
if (!is_dir($todayDir)) {
    mkdir($todayDir);
    $todayFile = $todayDir . $sep . $day . ".php";
    copy($baseFile, $todayFile);
    touch($todayDir . $sep . "input");
    touch($todayDir . $sep . "test");
}
