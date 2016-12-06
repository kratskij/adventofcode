<?php

$test = false;

$file = ($test) ? "../test.txt" : "input.txt";
$input = explode("\n", trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file)));

$regex = "//";

$values = [];
foreach ($input as $row) {
	$x = str_split($row);
	foreach ($x as $idx => $v) {
		$values[$idx][] = $v;
	}
}


$counted = $most = $fewest = [];
foreach ($values as $v) {
	$counted = [];
	foreach ($v as $letter) {
		if (!isset($counted[$letter])) {
			$counted[$letter] = 0;
		}
		$counted[$letter] += 1;
	}
	uksort($counted, function($a, $b) use ($counted) {
		return ($counted[$a] == $counted[$b]) ? strcmp($a, $b) : $counted[$a] < $counted[$b];
	});
	$keys = array_keys($counted);
	$most[] = array_shift($keys);
	$fewest[] = array_pop($keys);
}

echo "Part 1: " . implode("", $most) . "\n";
echo "Part 2: " . implode("", $fewest) . "\n";


#var_dump($value);
