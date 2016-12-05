<?php

$test = false;

$file = ($test) ? "../test.txt" : "input.txt";
$input = explode("\n", trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file)));

$regex = "/^([a-z\-]+)\-(\d+)\[([a-z]{5})\]$/";

$realRooms = [];

foreach ($input as $row) {
	preg_match($regex, $row, $matches);
	array_shift($matches); # remove first match (which is the whole matched string)

	$letters = str_split(str_replace("-", "", $matches[0]));
	$roomnumber = (int)$matches[1];
	$checksum = $matches[2];

	$prev = false;
	$counted = [];
	foreach ($letters as $letter) {
		if (!isset($counted[$letter])) {
			$counted[$letter] = 0;
		}
		$counted[$letter] += 1;
	}
	uksort($counted, function($a, $b) use ($counted) {
		return ($counted[$a] == $counted[$b]) ? strcmp($a, $b) : $counted[$a] < $counted[$b];
	});

	$test = implode("", array_slice(array_keys($counted), 0, 5));
	if ($test == $checksum) {
		$str = "";
		foreach(str_split($matches[0]) as $char) {
			$ord = ($char == "-") ? ord(" ") : ord($char) + ($roomnumber % 26);
			if ($ord >= 123) {
				$ord -= 26;
			}
			$str .= chr($ord);
		}

		$realRooms[str_replace(".", " ", $str)] = $roomnumber;
	}
}
$north = array_filter(
	$realRooms,
	function($key) {
		return (strpos($key, "north") >= -1);
	},
	ARRAY_FILTER_USE_KEY
);

echo "Part 1: " . array_sum($realRooms) . "\n";
echo "Part 2: " . array_pop($north) . "\n";
