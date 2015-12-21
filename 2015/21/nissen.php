<?php

$boss = [ "hitpoints" => 109, "damage" => 8, "armor" => 2 ];

$weapons = [
	"Dagger" =>     [ 8,  4, 0 ],
	"Shortsword" => [ 10, 5, 0 ],
	"Warhammer" =>  [ 25, 6, 0 ],
	"Longsword" =>  [ 40, 7, 0 ],
	"Greataxe" =>   [ 74, 8, 0 ]
];

$armors = [
	"Nothing" => 	[   0, 0, 0 ],
	"Leather" =>    [  13, 0, 1 ],
	"Chainmail" =>  [  31, 0, 2 ],
	"Splintmail" => [  53, 0, 3 ],
	"Bandedmail" => [  75, 0, 4 ],
	"Platemail" =>  [ 102, 0, 5 ]
];

$rings = [
	"Nothing" =>  [  0,  0, 0 ],
	"Damage1" =>  [  25, 1, 0 ],
	"Damage2" =>  [  50, 2, 0 ],
	"Damage3" =>  [ 100, 3, 0 ],
	"Defense1" => [  20, 0, 1 ],
	"Defense2" => [  40, 0, 2 ],
	"Defense3" => [  80, 0, 3 ]
];

$minCost = PHP_INT_MAX;
$maxCost = 0;
foreach ($weapons as $weapon) {
	foreach ($armors as $armor) {
		foreach ($rings as $k => $ring) {
			foreach ($rings as $l => $r) {
				if ($l == $k && $l != "Nothing") continue;
				$me = [
					"hitpoints" => 100,
					"damage" => $weapon[1] + $ring[1] + $r[1],
					"armor" => $armor[2] + $ring[2] + $r[2]
				];
				$cost = $weapon[0] + $armor[0] + $ring[0] + $r[0];
				if (play($me, $boss)) {
					$minCost = min($minCost, $cost);
				} else {
					$maxCost = max($maxCost, $cost);
				}
			}
		}
	}
}
echo "Part 1: " . $minCost . "\n";
echo "Part 2: " . $maxCost . "\n";

function play($me, $boss) {
	$myturn = true;
	while ($me["hitpoints"] > 0 && $boss["hitpoints"] > 0) {
		if ($myturn) {
			$boss["hitpoints"] -= max(1, $me["damage"] - $boss["armor"]);
		} else {
			$me["hitpoints"] -= max(1, $boss["damage"] - $me["armor"]);
		}
		$myturn = !$myturn;
	}

	return !$myturn;
}