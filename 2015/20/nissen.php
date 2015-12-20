<?php
$limit = 36000000;

for ($house = 1; presents($house, 10) < $limit; $house++);
echo "Part 1: $house\n";

for ($house = 1; presents($house, 11, 50) < $limit; $house++);
echo "Part 2: $house\n";


function presents($house, $presentsPerElf, $maxHouses = false) {
	$elfs = [];
	for ($mod = 1; $mod <= sqrt($house); $mod++) {
		if ($house % $mod == 0) {
			$elfs[$mod] = true;
			$elfs[$house/$mod] = true;
		}
	}
	if ($maxHouses) {
		static $housesVisited;
		if ($housesVisited === null) $housesVisited = [];

		foreach (array_keys($elfs) as $elf) {
			if (!isset($housesVisited[$elf])) $housesVisited[$elf] = 0;
			if ($housesVisited[$elf] == $maxHouses) unset($elfs[$elf]);
			else $housesVisited[$elf]++;
		}
	}

	$ret = array_sum(array_keys($elfs)) * $presentsPerElf;
#	echo "$house: $ret\n";
	return $ret;
}