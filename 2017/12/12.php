<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";

$file = ($test) ? "test" : "input";
$input = explode("\n", trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file)));

foreach ($input as $k => $line) { #$matches?
    $p = preg_split("/\s<->\s/", $line);
    $values[$p[0]] = array_map("intval", explode(", ", $p[1]));
}

$done = false;
while ($done != true) {
	$done = true;
	foreach ($values as &$v) {
		foreach ($v as $c) {
			foreach ($values[$c] as $nv) {
				if (!in_array($nv, $v)) {
					$done = false;
					$v[] = $nv;
				}
			}
		}
	}
}

$hasZero = array_filter(
		$values,
		function($v) {
			return in_array(0, $v);
		}
	);

echo "Part 1: ".count($hasZero)."\n";
$groups = array_map(function($v) { sort($v); return json_encode($v); }, $values);
echo "Part 2: " . (count(array_unique($groups))) . "\n";

