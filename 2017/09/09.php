<?php

ini_set('memory_limit','2048M');

$test = false;

$file = ($test) ? "test" : "input";
$input = explode("\n", trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file)));
$chars = str_split($input[0]);

$groups = [];
$groupPath = [];
$garbage = false;
$ignoreNext = false;
$sum = 0;
$level = 0;
$garbageCount = 0;

foreach ($chars as $k => $char) { #$matches?
	if ($ignoreNext) {
		$ignoreNext = false;
		continue;
	}
	if ($garbage) {
		switch ($char) {
			case ">":
				$garbage = false;
				break;
			case "!":
				$ignoreNext = true;
				break;
			default:
				$garbageCount += 1;
		}
		continue;

	}
	switch ($char) {
		case ",":
			break;
		case "{": //new group
			$currGroup = $k;
			$level++;
			break;
		case "}":
			$sum += $level;
			$level--;
			break;
		case "<":
			$garbage = true;
			break;
		case "!":
			$ignoreNext = true;
			break;
	}
}

echo "Part 1: " . $sum . "\n";
echo "Part 2: " . $garbageCount . "\n";
