<?php

$test = false;

$file = ($test) ? "../test.txt" : "input.txt";
$input = preg_replace("/\s/", "", trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file)));
$regex = "/\(\d+x\d+\)/";
$values = [];
echo "original input length: " . strlen($input) . "\n";
$input = decompress($input);
echo "ending input length: " . $input."\n";

function decompress($input, $multiply = 1) {
	$parenthesisPos = strpos($input, "(");
	if ($parenthesisPos === false) { //nothing to decode
		return strlen($input) * $multiply;
	}
	for ($k = 0; $k < strlen($input); $k++) {
		$i = $input[$k];
		$first = $second = "";
		if ($i == "(") {
			$x = 1;
			while(true) {
				if ($input[$k+$x] == "x") {
					break;
				}
				$first .= $input[$k+$x];
				$x++;
			}
			$x++;
			while(true) {
				if ($input[$k+$x] == ")") {
					break;
				}
				$second .= $input[$k+$x];
				$x++;
			}
			$first = (int)$first;
			$second = (int)$second;

			$charlen = strlen("(".$first."x".$second.")");
			#echo "NEW: " . "(".$first."x".$second.")"."\n";
			$insert = substr($input, $k + $charlen, $first);
			$firstArr = substr($input, 0, $k);
			$secondArr = substr($input, $k + $charlen + strlen($insert));
			return strlen($firstArr) + ($second * decompress($insert, $multiply)) + decompress($secondArr, $multiply);
		}
	}
}
