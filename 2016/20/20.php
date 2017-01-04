<?php

$input = explode("\n", trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "input.txt")));
$regex = "/(\d+)\-(\d+)/";
$blocked = [];

foreach ($input as $row) {
	preg_match($regex, $row, $matches);
	array_shift($matches); # remove first match (which is the whole matched string)
	$matches = array_map("intval", $matches);

	$blocked[$matches[0]] = $matches[1];
}
ksort($blocked);
while ($ret = mergeAnOverlap($blocked)) {
	#echo $ret . "\n";
}
reset($blocked);

echo "Part 1: " . ($blocked[key($blocked)] + 1) . "\n";
echo "Part 2: " . countAllowed($blocked, pow(2,32)) . "\n";

function mergeAnOverlap(array &$blocked)
{
	$prev = $blocked;
	foreach ($blocked as $min => $max) {
		foreach ($prev as $min2 => $max2) {
			if ($min2 < $min && $max2 > $max) {
				//replace that tiny range
				unset($blocked[$min]);
				return "Removed $min-$max: (because $min2-$max2)";
			}

			if ($min2 < $min && $max2 >= $min) {
				//alter downrange
				unset($blocked[$min]);
				$blocked[$min2] = max($max, $max2);
				return "Altered downrange $min-$max -> $min2-" . $blocked[$min2] . "";
			}
			if ($min2 <= $max + 1 && $max2 > $max) {
				$blocked[$min] = $max2;
				return "Altered upper $min-$max -> $min-$max2";
			}
		}
	}
	return false;
}

function countAllowed($blocked, $maximum) {
	$sumAllowed = 0;
	$prevMax = -1;
	foreach ($blocked as $min => $max) {
		$sumAllowed += ($min - $prevMax - 1);
		$prevMax = $max;
	}

	return $sumAllowed + $maximum - 1 - $max;
}
