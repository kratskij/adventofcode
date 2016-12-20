<?php

$input = 0;
$input = 0; //for testing

$test = false;

$file = ($test) ? "test.txt" : "input.txt";
$input = explode("\n", trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file)));

#$input = ["5-8", "0-2", "4-7"];

$regex = "/(\d+)\-(\d+)/";

$values = [];
$lowestBlocked = 0;
$prev = false;
$allowed = [];
$prevAllowed = 0;

foreach ($input as $row) {
	preg_match($regex, $row, $matches);
	array_shift($matches); # remove first match (which is the whole matched string)
	$matches = array_map("intval", $matches);

	$allowed[$matches[0]] = $matches[1];
}

ksort($allowed);
while ($ret = mergeAnOverlap($allowed)) {
	#echo $ret;
	ksort($allowed);
}
reset($allowed);

echo "Part 1: " . ($allowed[key($allowed)] + 1) . "\n";
echo "Part 2: " . countMissing($allowed, pow(2,32)) . "\n";

function mergeAnOverlap(array &$allowed)
{
	$prevAllowed = $allowed;
	foreach ($allowed as $min => $max) {
		foreach ($prevAllowed as $min2 => $max2) {
			if ($min2 < $min && $max2 > $max) {
				//replace that tiny range
				unset($allowed[$min]);
				return "Removed $min-$max: (because $min2-$max2)\n";
			}

			if ($min2 < $min && $max2 >= $min) {
				//alter downrange
				unset($allowed[$min]);
				$allowed[$min2] = max($max, $max2);
				return "Altered downrange $min-$max -> $min2-" . $allowed[$min2] . "\n";
			}
			if ($min2 <= $max && $max2 > $max) {
				$allowed[$min] = $max2;
				return "Altered upper $min-$max -> $min-$max2\n";
			}
		}
	}
	return false;
}

function countMissing($allowed, $maximum) {
	$sumAllowed = 0;
	$prevMax = -1;
	foreach ($allowed as $min => $max) {
		$sumAllowed += ($min - $prevMax - 1);
		$prevMax = $max;
	}

	return $sumAllowed + $maximum - 1 - $max;
}

#var_dump($value);
