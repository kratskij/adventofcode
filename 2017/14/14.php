<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$input = ($test) ? "flqrgnkx" : "ugkiagan";

$values = range(0, 127);
foreach ($values as $k => $row) {
    $hash = getHash($input."-".$k);
    $str = "";
    foreach (str_split($hash) as $c) {
        $str .= str_pad(decbin(hexdec($c)), 4, "0", STR_PAD_LEFT);
    }
    $values[$k] = str_split($str);
}

echo "Part 1: " . array_sum(array_map(function($v) { return count(array_filter($v)); }, $values)) . "\n";

$group = 0;
foreach ($values as $v => &$rowe) {
    foreach ($rowe as $c => &$colz) {
        if ($colz === "1") {
            $group++;
            $values[$v][$c] = $group;
            setNeighbours($values, $v, $c);
        }
    }
}

echo "Part 2: $group\n";

function setNeighbours(&$values, $row, $col) {
    foreach ([[$row+1,$col], [$row-1,$col], [$row,$col+1], [$row,$col-1]] as $i) {
        list($r,$c) = $i;
        if (isset($values[$r][$c]) && $values[$r][$c] === "1") {
            $values[$r][$c] = $values[$row][$col];
            setNeighbours($values, $r,$c);
        }
    }
}

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
		$string .= str_pad(dechex($h), 2, "0", STR_PAD_LEFT);
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
