<?php

$test = false;

$file = ($test) ? "../test.txt" : "input.txt";
$doorId = trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file));

$c = 0;
$hash = "";
$realHash = [];
while (true) {
	$c++;
	if (substr(md5($doorId.$c), 0, 5) === "00000") {
		$characters = str_split(md5($doorId.$c));
		$hash .= $characters[5];
		if (is_numeric($characters[5]) && (int)$characters[5] < 8 && !isset($realHash[$characters[5]])) {
			$realHash[$characters[5]] = $characters[6];
		}
		if (count($realHash) == 8) {
			break;
		}
	}
}
ksort($realHash);

echo "Part 1: " . substr($hash, 0, 8) . "\n";
echo "Part 2: " . implode("", $realHash) . "\n";
