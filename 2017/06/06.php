<?php

ini_set('memory_limit','2048M');

$test = false;

$file = ($test) ? "test" : "input";
$input = explode("\n", trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file)));
#$input = str_split($input[0]);
#$input = preg_match ("//" , $input, &$matches)
#var_dump($input);
$input = array_map("intval", $input);

$regex = "//";
$values = [];
$outString = "";
$sum = 0;
$c = 0;
$len = count($input);

function redistribute(&$input, &$stopAt) {
	$c = 0;
	$seen = [];
	while (true) {
		$c++;
		$max = max($input);
		$idx = array_search($max, $input);
		$input[$idx] = 0;

		$i = $idx;
		while ($max > 0) {
			$i += 1;
			if (!isset($input[$i])) {
				$i = 0;
			}
			$input[$i]++;
			$max--;
		}
		$hash = md5(json_encode($input));
		if ($stopAt !== false) {
			if ($hash == $stopAt) {
				return $c;
			}
		} else if (isset($seen[$hash])) {
			$stopAt = $hash;
			return $c;
		}
		$seen[$hash] = true;
	}
}
$hash = false;
echo redistribute($input, $hash)."\n";
#echo $hash;
echo redistribute($input, $hash)."\n";