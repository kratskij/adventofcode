<?php
$test = [
		["HM", "LM"],
		["HG"],
		["LG"],
		[]
];
$part1 = [
	["SG","SM","PG","PM"],
	["TG","RG","CG","RM","CM"],
	["TM"],
	[]
];
$part2 = [
	["SG","SM","PG","PM","EG","EM","DE","DM"],
	["TG","RG","CG","RM","CM"],
	["TM"],
	[]
];

echo "Test: " . calculateSteps($test) . "\n";
echo "Part 1: " . calculateSteps($part1) . "\n";
echo "Part 2: " . calculateSteps($part2) . "\n";

function calculateSteps($floors)
{
	$steps = 0;
	for ($i = 0; $i < count($floors); $i++) {
		if (isset($floors[$i + 1])) {
			$steps += 2 * (count($floors[$i]) - 2) + 1;
			$floors[$i + 1] = array_merge($floors[$i + 1], $floors[$i]);
		}
	}
	return $steps;
}
