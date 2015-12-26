<?php
$t = 20151125; $r = 2947; $c = 3029; $mul = 252533; $mod = 33554393;
$x = $r + $c;
$iter = ($x ** 2 - $x * 3) / 2 + $c;
for ($i = 0; $i < $iter; $i++) { $t = $t * $mul % $mod; }
echo $t."\n";