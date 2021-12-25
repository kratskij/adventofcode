<?php

#                1   1    1      1    1      8    9      7    9   8   9   8   5   1
$xAdditions = [ 11, 14,  10,    14,  -8,    14, -11,    10,  -6, -9, 12, -5, -4, -9 ];
$yAdditions = [  7,  8,  16,     8,   3,    12,   1,     8,   8, 14,  4, 14, 15,  6 ];
$zDivisions = [  1,  1,   1,     1,  26,     1,  26,     1,  26, 26,  1, 26, 26, 26 ];

#         w=0    1   1    1      1    1      8    9      7    9   8    9   8  5   1
#         x=0    1   1    1      1    0      1    0      1    0   0    1   0  0   1
#         y=0    8   9   17      9    0     20    0     15    0   0   13   0  0   7
#         z=0    8 217 5659 147143 5659 147154 5659 147149 5659 217 5655 217  8   7

$num1 = 1000;
$midSection = 99897999; # found by looking for low z values through even more bruteforcey methods

while ($num1 >= 112) {
    while (strpos(--$num1, "0"));
    $num2 = 1000;
    while ($num2 >= 112) {
        while (strpos(--$num2, "0"));
        if (checkNum((int)($num1.$midSection.$num2), $xAdditions, $yAdditions, $zDivisions) == 0) {
            $p1 = $num1.$midSection.$num2;
            break 2;
        }
    }
}

$num1 = 11111110;
$startSection = 311111;  # found by looking for low z values through even more bruteforcey methods
while ($num1 <= 99999998) {
    while (strpos(++$num1, "0") > 0);
    if (checkNum((int)($startSection . $num1), $xAdditions, $yAdditions, $zDivisions) == 0) {
        $p2 = $startSection . $num1;
        break;
    }
}
echo "p1: $p1\np2: $p2\n";


function checkNum($nums, &$xAdditions, &$yAdditions, &$zDivisions) {
    $w = $x = $y = $z = 0;
    for ($i = 0; $i < 14; $i++) {
        $w = ((string)$nums)[$i];
        $x = ((($z % 26) + $xAdditions[$i]) == $w) ? 0 : 1;
        $y = ($w + $yAdditions[$i]) * $x;
        $z = (($z > 0) ? (int)floor($z / $zDivisions[$i]) : (int)ceil($z / $zDivisions[$i]))*(25*$x + 1) + $y;
    }

    return $z;
}
