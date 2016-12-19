<?php

$test = true;

$input = 3014387;

$input = 5;

$left = [];
$left2 = [];
for ($i = 1; $i <= $input; $i++) {
	$left[$i] = $i;
	$left2[$i] = $i;
}
$nextDel = false;

while (false && count($left) > 1) {
	foreach($left as $i => $l) {
		if ($nextDel) {
	#		echo "$i\n";
			$left[$i] = $nextDel = false;
		} else if ($l !== false) {
	#		echo "$i taking from ";
			$nextDel = true;
		}
	}
	$left = array_filter($left);
	echo "left : " . count($left) . "\n";
}
#echo "Part 1: ";
#var_Dump($left);

while (count($left2) > 1) {
	foreach($left2 as $i => $l) {
		$mid = ceil(count($left2) / 2);

		$key = ($i < $mid) ? $i + $mid - 1 : $i - $mid;
		echo "$l taking from " . $left2[$key] . "\n";
		var_dump($i, $l, $key, $mid, $left2[$key]);
		echo "-----------\n";
		unset($left2[$key]);# = false;
		$left2 = array_filter($left2);


		sort($left2);
	}
		#var_dump($left2);
	echo "left : " . count($left2) . "\n";
}
var_dump($left2);
