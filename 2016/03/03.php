<?php

$test = false;

$file = ($test) ? "../test.txt" : "input.txt";
$input = explode("\n", trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file)));

$regex = "/^\s*(\d+)\s+(\d+)\s+(\d+)\s*$/";

$values = [];

$tmp1 = $tmp2 = $tmp3 = $part2 = [];

foreach ($input as $row) {
	preg_match($regex, $row, $matches);
	array_shift($matches); # remove first match (which is the whole matched string)
	$matches = array_map("intval", $matches);

	$part1[] = $matches;

	$tmp1[] = $matches[0];
	$tmp2[] = $matches[1];
	$tmp3[] = $matches[2];

	if (count($tmp1) == 3) {
		$part2[] = $tmp1;
		$part2[] = $tmp2;
		$part2[] = $tmp3;
		$tmp1 = $tmp2 = $tmp3 = [];
	}
}

function findPossibleTriangles($rows) {
	$possible = 0;
	foreach ($rows as $matches) {
		$hyp = max($matches);
		$katz = [];
		foreach ($matches as $match) {
			if ($match < $hyp) {
				$katz[] = $match;
			}
		}
		if (count($katz) != 2 || array_sum($katz) > $hyp) {
			$possible += 1;
		}
	}
	return $possible;
}

echo "Part 1: " . findPossibleTriangles($part1) ."\n";
echo "Part 2: " . findPossibleTriangles($part2) ."\n";
