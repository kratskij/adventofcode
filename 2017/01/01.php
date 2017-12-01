<?php

$test = false;

$file = ($test) ? "test" : "input";
$input = explode("\n", trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file)));

$regex = "//";
$values = [];
$outString = "";

$input = str_split($input[0]);

$parts = [
	1 => 0,
	2 => 0
];

$ahead = count($input) / 2;

foreach ($input as $k => $char) {
	//part1
	if (!isset($input[$k+1])) {
		$next = $input[0] ;
	} else {
		$next = $input[$k+1];
	}
	if ($char == $next) {
		$parts[1] += (int)$char;
	}

	//part 1
	if (!isset($input[$k+$ahead])) {
		$next = $input[$k-$ahead] ;
	} else {
		$next = $input[$k+$ahead];
	}
	if ($char == $next) {
		$parts[2] += (int)$char;
	}


}

foreach ($parts as $k => $p) {
	echo "Part $k: $p\n";
}
