<?php

$file = "input.txt";
$input = explode(", ", file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file));

$regex = "/([LR])(\d+)/";

$directions = ["N" => 1, "E" => 1, "S" => -1, "W" => -1];

$dirIdx = $NS = $EW = $npos = $lpos = 0;

$visited = [];
$firstTwice = false;
foreach ($input as $row) {
	preg_match($regex, $row, $matches);
	list(, $turn, $length) = $matches;

	$dirIdx += ($turn == "R") ? 1 : -1;
	if ($dirIdx == -1) {
		$dirIdx = 3;
	}

	$direction = array_keys($directions)[$dirIdx % 4];

	if (in_array($direction, ["N", "S"])) {
		$ref = &$NS;
	} else {
		$ref = &$EW;
	}

	for ($i = 0; $i < $length; $i++) {
		if (!$firstTwice && isset($visited[$NS . "_" . $EW])) {
			$firstTwice = [$NS, $EW];
		} else {
			$visited[$NS . "_" . $EW] = true;
		}
		$ref += $directions[$direction];
	}
}

echo "Part 1: " . (abs($NS) + abs($EW)) . "\n";
echo "Part 2: " . (abs($firstTwice[0]) + abs($firstTwice[1])) . "\n";
