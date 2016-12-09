<?php

$test = false;

$file = ($test) ? "../test.txt" : "input.txt";
$input = preg_replace("/\s/", "", trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file)));
#$input = "X(10x10)(3x3)ABCYX(2x10)ABC";
$regex = "/\(\d+x\d+\)/";

$values = [];
echo "original input length: " . strlen($input) . "\n";
#echo $input."\n";

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

		$charlen = strlen("(".$first."x".$second.")");

		$insert = substr($input, $k + $charlen, (int)$first);
		$firstArr = substr($input, 0, $k);
		$secondArr = substr($input, $k + $charlen);

		$combined = "";
		for($num = 0; $num < (int)$second - 1; $num++) {
			$combined .= $insert;
		}
		echo $combined."\n"; sleep(1);
		$input = $firstArr . $combined . $secondArr;
		$k += strlen($combined);
	}
}
$input = preg_replace("/\s/", "", $input);
echo $input."\n";
echo "ending input length: " . strlen($input)."\n";

/*
preg_match_all($regex, $input, $matches);
$matches = $matches[0];

$c = strlen($input);
foreach ($matches as $match) {
	preg_match("/\((\d+)x(\d+)\)/", $match, $m);
	array_shift($m);
	$c += (int)$m[0]*(int)$m[1] - strlen($match);
}

echo $c;
*/

#var_dump($value);
