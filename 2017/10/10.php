<?php

$byteSequence = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "input");

echo "Part 1: " . array_product(array_slice(solve(explode(",", $byteSequence), 256), 0, 2)) . "\n";
echo "Part 2: " . getHash($byteSequence) . "\n";

function getHash($byteSequence) {
	$values = array_map("ord", str_split($byteSequence));
	$values = array_merge($values, [17, 31, 73, 47, 23]);
	$lengths = solve($values, 256, 64);

	$denseHash = [];
	for ($i = 0; $i < 16; $i++) {
		$slice = array_slice($lengths, $i * 16, 16);
		$bitz = array_shift($slice);
		foreach ($slice as $s) {
			$bitz = $bitz ^ $s;
		}
		$denseHash[] = $bitz;
	}

	$string = "";
	foreach ($denseHash as $h) {
		$string .= dechex($h);
	}

	return $string;
}

function solve($values, $listSize, $repeats = 1)
{
	$lengths = range(0, $listSize - 1);
	$curPos = 0;
	$skipSize = 0;
	for ($i = 0; $i < $repeats; $i++) {
		foreach ($values as $val) {
			flip($lengths, $val, $curPos, $skipSize);
		}
	}
	return $lengths;
}

function flip(&$lengths, $val, &$curPos, &$skipSize)
{
	if ($val > 1) {
		if ($curPos + $val > count($lengths)) {
			$end = array_slice($lengths, $curPos);
			$start = array_slice($lengths, 0, $val - count($end));
			$slice = array_merge($end, $start);
			$slice = array_reverse($slice);
			$end = array_slice($slice, 0, count($end));
			$start = array_slice($slice, count($end));
			array_splice($lengths, $curPos, count($end), $end);
			array_splice($lengths, 0, count($start), $start);
		} else {
			$slice = array_slice($lengths, $curPos, $val);
			$newSlice = array_reverse($slice);
			array_splice($lengths, $curPos, $val, $newSlice);
		}
	}
	$curPos = ($curPos + $val + $skipSize) % count($lengths);
	$skipSize++;
}