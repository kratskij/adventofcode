<?php

$input = explode("\n", trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "input.txt")));
$regex = '/Disc #(\d+) has (\d+) positions; at time=(\d+), it is at position (\d+)./';

$animate = false;

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
		if ($animate && $time % (97 * $part * $part * $part) == 0) {
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
	if ($animate) {
		$vTime = $time - 20;
		while ($vTime < $time) {
			visualize($part, $discs[$part], $vTime, $prefix . "Part: $part\nTime: $vTime\n", 500000 / ($time - $vTime));
			$vTime++;
		}
		for ($i = 1; $i <= count($discs[$part]) + 1; $i++) {
			visualize($part, $discs[$part], $vTime, $prefix . "Part: $part\nTime: $vTime (+$i)\n", 1000000, $i);
		}
		$i--;
		$prefix = visualize($part, $discs[$part], $vTime, $prefix . "Part: $part\nTime: $vTime (+$i)\n", false, $i)."\n";
	} else {
		echo "Part $part: $time\n";
	}
}

function visualize($part, $discs, $time, $prefix = "", $delay = false, $capsulePos = false)
{
	$str = "$prefix\n";
	foreach ($discs as $disc) {
		if ($disc["delay"] == 1) {
			$str .= $capsulePos === false ? "   *\n" : "\n";
		}
		$str .= $disc["delay"] . " |";
		for ($i = 0; $i < $disc["positions"]; $i++) {
			$str .= (($disc["startPos"] + $time + $capsulePos) % $disc["positions"] != $i) ? "█" : ($capsulePos !== false && $capsulePos == $disc["delay"] ? "*" : " ");
		}
		$str .= "|\n";
	}
	$str .= ($capsulePos > count($discs) ? "   *" : "") . "\n";
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
