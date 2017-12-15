<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";

$aVal = ($test) ? 65 : 783;
$bVal = ($test) ? 8921 : 325;

$sum = 0;

for ($i = 0; $i < 40000000; $i++) {
    $aVal = ($aVal * 16807) % 2147483647;
    $bVal = ($bVal * 48271) % 2147483647;
    if (($aVal & 65535) == ($bVal & 65535)) {
        $sum++;
    }
}
echo "Part 1: " . $sum . "\n";


$aVal = ($test) ? 65 : 783;
$bVal = ($test) ? 8921 : 325;

$sum = $i = 0;

while (true) {
    $aVal = ($aVal * 16807) % 2147483647;
    #echo $aVal . ": " . ($aVal & 3)."\n";
    if (($aVal & 3) == 0) {
        while (true) {
            $bVal = ($bVal * 48271) % 2147483647;
            if (($bVal & 7) == 0) {
                $i++;
                if (($aVal & 65535) == ($bVal & 65535)) {
                    $sum++;
                    #echo "$sum@$i\n";
                }

                if ($i == 5000000) {
                    break 2;
                }
                break;
            }
        }
    }
}
echo "Part 2: " . $sum . "\n";
