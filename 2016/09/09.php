<?php

$input = preg_replace("/\s/", "", trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "input.txt")));

echo "Part 1: " . decompress($input, true) . "\n";
echo "Part 2: " . decompress($input, false) . "\n";

function decompress($input, $onlyonce = 1) {
	$parenthesisPos = strpos($input, "(");
	if ($parenthesisPos === false) { //nothing to decode
		return strlen($input);
	}

	preg_match("/\((\d+)x(\d+)\)/", $input, $matches);
	list($all, $first, $second) = $matches;

	$insert = substr($input, $parenthesisPos + strlen($all), $first);
	$firstStr = substr($input, 0, $parenthesisPos);
	$secondStr = substr($input, $parenthesisPos + strlen($all) + strlen($insert));

	return
		strlen($firstStr) +
		($second * (
			$onlyonce
			? strlen($insert)
			: decompress($insert, $onlyonce)
		)) +
		decompress($secondStr, $onlyonce)
	;
}
