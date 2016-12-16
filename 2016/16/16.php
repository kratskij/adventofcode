<?php

$test = false;

$file = ($test) ? "../test.txt" : "input.txt";
$input = explode("\n", trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file)));

$input = "10111100110001111";
$length = 35651584;

//TEST
#$input = "10000"; $length = 20;

$regex = "//";

$values = [];

$out = "";
while (strlen($input) != $length) {
	$tmp = $input;
	$tmp = strrev($tmp);
	$tmp = str_replace("a", "1", str_replace("1", "0", str_replace("0", "a", $tmp)));

	$input = $input . "0" . $tmp;

	$input = substr($input, 0, $length);
	#echo $input . "\n";
}

$checksum = $input;
$i = 0;
while (strlen($checksum) % 2 != 1) {
	$out = "";
	while (isset($checksum[$i+1])) {
		$out .= ($checksum[$i] == $checksum[$i+1]) ? "1" : "0";
		$i += 2;
	}
	$checksum = $out;
	echo "c: " . strlen($checksum) . "\n";
	$i = 0;
}

echo "\nc: ".$checksum . "\n";
