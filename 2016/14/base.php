<?php

$test = true;

$file = ($test) ? "../test.txt" : "input.txt";
$salt = "qzyelonm";
#$salt = "abc";

$values = [0 => [], 1 => []];

$keep = [0 => [], 1 => []];

$i = 0;
while(count($values[0]) < 70 || count($values[1]) < 70) {
	$r[0] = md5($salt.$i);
	$r[1] = $r[0];
	for ( $x = 0; $x < 2016; $x++) {
		$r[1] = md5($r[1]);
	}
	foreach (range(0,1) as $part) {
		$keep[$part] = array_filter(
			$keep[$part],
			function($a) use ($i) {
				return $i - $a < 1000;
			},
			ARRAY_FILTER_USE_KEY
		);
		preg_match("/(" . implode("|", array_unique($keep[$part])) . ")/", $r[$part], $m);

		if (isset($m[0]) && strlen($m[0])) {
			foreach ($keep[$part] as $j => $k) {
				preg_match("/($k)/", $r[$part], $m2);
				if (isset($m2[1]) && strlen($m2[1])) {
					$values[$part][$j] = $r[$part] . " @ " . $i . " @ " . $j;
					unset($keep[$part][$j]);
					#echo count($values[0]) . ":" . count($values[1]) . "\n";
				}
			}
		}
		preg_match("/(.)\\1\\1/", $r[$part], $matches);
		if (isset($matches[1]) && strlen($matches[1])) {
			#echo "found {$matches[1]} @ $i\n";
			$keep[$part][$i] = $matches[1].$matches[1].$matches[1].$matches[1].$matches[1];
		}
	}

	$i++;
}


ksort($values[0]);
ksort($values[1]);
#var_Dump($values);
echo "Part 1: " . array_keys($values[0])[63]."\n";
echo "Part 2: " . array_keys($values[1])[63]."\n";
