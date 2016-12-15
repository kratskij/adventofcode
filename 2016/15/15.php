<?php

$input = explode("\n", trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "input.txt")));
$regex = '/Disc #(\d+) has (\d+) positions; at time=(\d+), it is at position (\d+)./';

$visualize = true;

$discs = [
	1 => [], // Part 1
	2 => [], // Part 2
];

foreach ($input as $i) {
	preg_match($regex, $i, $matches);
	list(,$delay,$positions,$time,$startPos) = $matches;
	$discs[1][] = [
		"delay" => (int)$delay,
		"positions" => (int)$positions,
		"startPos" => (int)$startPos
	];
}
$discs[2] = array_merge($discs[1], [["delay" => 7, "positions" => 11, "startPos" => 0]]);

$prefix = "";
foreach(range(1,2) as $part) {
	$time = 0;
	while (true) {
		if ($visualize && $time % (97 * $part * $part * $part) == 0) {
			visualize($part, $discs[$part], $time, $prefix . "Part: $part\nTime: $time\n");
		}
		foreach ($discs[$part] as $disc) {
			if (($disc["startPos"] + $time + $disc["delay"]) % $disc["positions"] != 0) {
				$time++;
				continue 2;
			}
		}
		break;
	}
	if ($visualize) {
		$vTime = $time - 20;
		while ($vTime < $time) {
			visualize($part, $discs[$part], $vTime, $prefix . "Part: $part\nTime: $vTime\n", 500000 / ($time - $vTime));
			$vTime++;
		}
		$prefix = visualize($part, $discs[$part], $vTime, $prefix . "Part: $part\nTime: $vTime\n")."\n";
	} else {
		echo "Part $part: $time\n";
	}
}

function visualize($part, $discs, $time, $prefix = "", $delay = false)
{
	$str = "$prefix\n";
	foreach ($discs as $disc) {
		$str .= $disc["delay"] . " |";
		for ($i = 0; $i < $disc["positions"]; $i++) {
			$str .= (($disc["startPos"] + $time + $disc["delay"]) % $disc["positions"] != $i) ? "█" : " ";
		}
		$str .= "|\n";
	}
	display($str."\n", $delay);
	return $str;
}

function display($string, $delay = false)
{
	system("clear");
	echo $string."\n";
	if ($delay !== false) {
		usleep($delay);
	}
}
