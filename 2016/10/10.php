<?php

$test = false;

$file = ($test) ? "../test.txt" : "input.txt";
$input = explode("\n", trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file)));

$bots = $outputs = $tryagain = [];

while(count($input) > 0) {
	foreach ($input as $row) {
		switch (substr($row, 0, 3)) {
			case "bot":
				preg_match("/bot (\d+) gives low to (bot|output) (\d+) and high to (bot|output) (\d+)/", $row, $matches);
				if (!isset($bots[$matches[1]]) || count($bots[$matches[1]]) < 2) {
					$tryagain[] = $row;
					break;
				}
				if ($matches[2] == "output") {
					$outputs[$matches[3]] = min($bots[$matches[1]]);
				} else {
					append($bots, $matches[3], min($bots[$matches[1]]));
				}
				if ($matches[4] == "output") {
					$outputs[$matches[5]] = max($bots[$matches[1]]);
				} else {
					append($bots, $matches[5], max($bots[$matches[1]]));
				}
				$bots[$matches[1]] = [];
				break;
			case "val":
				preg_match("/value (\d+) goes to bot (\d+)/", $row, $matches);
				append($bots, $matches[2], $matches[1]);
				break;
			default:
				echo "WTF\n";
		}
	}
	$input = $tryagain;
	$tryagain = [];
}

echo "Part 2: " . ($outputs[0] * $outputs[1] * $outputs[2]) . "\n";

function append(&$arr, $id, $val) {
	$id = (int)$id;
	$val = (int)$val;
	if (!isset($arr[$id])) {
		$arr[$id] = [];
	}
	$arr[$id][] = $val;

	if (in_array(17, $arr[$id]) && in_array(61, $arr[$id])) {
		echo "Part 1: " . $id . "\n";
	}
}
