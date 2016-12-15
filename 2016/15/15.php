<?php

$input = explode("\n", trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "input.txt")));
$regex = '/Disc #(\d+) has (\d+) positions; at time=(\d+), it is at position (\d+)./';

$discs = [
	1 => [], // Part 1
	2 => [], // Part 2
];

foreach ($input as $i) {
	preg_match($regex, $i, $matches);
	list(,$delay,$positions,$time,$startPos) = $matches;
	$discs[1][$delay] = [
		"delay" => (int)$delay,
		"positions" => (int)$positions,
		"startPos" => (int)$startPos
	];
}
$discs[2] = array_merge($discs[1], [["delay" => 7, "positions" => 11, "startPos" => 0]]);
var_Dump($discs[2]);
foreach(range(1,2) as $part) {
	$time = 0;
	while (true) {
		$time++;
		foreach ($discs[$part] as $disc) {
			if (($disc["startPos"] + $time + $disc["delay"]) % $disc["positions"] != 0) {
				continue 2;
			}
		}
		break;
	}
	echo "Part $part: $time\n";
}
