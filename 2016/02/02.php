<?php

$test = false;

$file = ($test) ? "../test.txt" : "input.txt";
$input = explode("\n", trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file)));

$pad1 = [
	[ 1, 2, 3 ],
	[ 4, 5, 6 ],
	[ 7, 8, 9 ],
];

$pad2 = [
	[null, null,  1, null,  null],
	[null,   2 ,  3,   4 ,  null],
	[   5,   6 ,  7,   8 ,  9   ],
	[null,  "A", "B", "C" , null],
	[null, null, "D", null, null],
];

echo "Part 1: " . getCode($input, $pad1) . "\n";
echo "Part 2: " . getCode($input, $pad2) . "\n";

function getCode($input, $pad, $startValue = 5) {
	foreach ($pad as $y => $row) {
		$x = array_search($startValue, $row);
		if ($x !== false) {
			break;
		}
	}

	$code = "";
	foreach ($input as $row) {
		$directions = str_split($row);
		foreach ($directions as $direction) {
			$tx = $x;
			$ty = $y;
			switch ($direction) {
				case "L":
					$tx -= 1;
					break;
				case "R":
					$tx += 1;
					break;
				case "U":
					$ty -= 1;
					break;
				case "D":
					$ty += 1;
					break;
			}
			if (isset($pad[$ty]) && isset($pad[$ty][$tx]) && !is_null($pad[$ty][$tx])) {
				$y = $ty;
				$x = $tx;
			}
		}
		$code .= $pad[$y][$x];
	}
	return $code;
}
