<?php

$test = false;

$file = ($test) ? "../test.txt" : "input.txt";
$input = explode("\n", trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file)));


$abba = array_filter($input, function($row) {
	$s = str_split($row);
	$valid = true;
	$found = false;
	foreach ($s as $k => $c) {
		if ($c == "[") {
			$valid = false;
		} else if ($c == "]") {
			$valid = true;
		}
		if (!isset($s[$k+3])) {
			return $found;
		}

		if ($s[$k+1] != $c && $s[$k+2] == $s[$k+1] && $s[$k+3] == $c) {
			if (!$valid) {
				return false;
			}
#			echo $row . "::" . $c . $s[$k+1] . $s[$k+2] . $s[$k+3] . "\n";
			$found = true;
		}
	}
	return $found;
});

$aba = array_filter($input, function($row) {
	$s = str_split($row);
	$hypernet = false;
	$found = [];
	$hf = [];
	foreach ($s as $k => $c) {
		if (!isset($s[$k+2])) {
			break;
		}

		if ($c == "[") {
			$hypernet = true;
		} else if ($c == "]") {
			$hypernet = false;
		}

		if ($s[$k+2] == $c && $s[$k+1] != $c) {
			if ($hypernet) {
				$hf[] = $s[$k+1].$c.$s[$k+1];
			} else {
				$found[] = $c.$s[$k+1].$c;
			}
#			echo $row . "::" . $c . $s[$k+1] . $s[$k+2] . $s[$k+3] . "\n";
		}
	}
	var_Dump($hf, $found);
	foreach ($hf as $h) {
		if (in_array($h, $found)) {
			return true;
		}
	}
	return false;
});

var_dump($aba);
echo "Part 1: " . count($abba);

var_dump($aba);
echo "Part 2: " . count($aba);
