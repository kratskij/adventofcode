<?php

$input = "10111100110001111";
$parts = [
	1 => 272,
 	2 => 35651584
];

foreach ($parts as $part => $length) {
	$output = $input;
	$out = "";
	while (strlen($output) != $length) {
		$tmp = $output;
		$tmp = strrev($tmp);
		$tmp = str_replace("a", "1", str_replace("1", "0", str_replace("0", "a", $tmp)));

		$output = $output . "0" . $tmp;

		$output = substr($output, 0, $length);
	}

	$checksum = $output;
	$i = 0;
	while (strlen($checksum) % 2 != 1) {
		$out = "";
		while (isset($checksum[$i+1])) {
			$out .= ($checksum[$i] == $checksum[$i+1]) ? "1" : "0";
			$i += 2;
		}
		$checksum = $out;
		#echo "c: " . strlen($checksum) . "\n";
		$i = 0;
	}
	echo "Part $part: $checksum\n";
}
