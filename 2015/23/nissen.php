<?php

$test = false;

$file = ($test) ? "test.txt" : "input";
$input = explode("\n", file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file));

$values = [];

echo "Part 1: " . compute(["a" => 0, "b" => 0], $input)["b"] . "\n";
echo "Part 2: " . compute(["a" => 1, "b" => 0], $input)["b"] . "\n";

function jump($val, &$input) {
	if ($val > 0) {
		for ($i = 1; $i < (int)$val; $i++) {
			next($input);
		}
	} else {
		for ($i = 1; $i > (int)$val; $i--) {
			prev($input);
		}
	}
}

function compute($registers, $input) {
	while ($row = current($input)) {
		#echo "$row\n";
		$parts = explode(" ", $row);
		$cmd = array_shift($parts);
		$reg = str_replace(",", "", array_shift($parts));
		$val = ($parts) ? (int)array_shift($parts) : false;

		switch ($cmd) {
			case "hlf":
				$registers[$reg] /= 2;
				break;
			case "tpl":
				$registers[$reg] *= 3;
				break;
			case "inc":
				$registers[$reg] += 1;
				break;
			case "jmp":
				jump($reg, $input);
				break;
			case "jie":
				if ($registers[$reg] % 2 == 0) {
					jump($val, $input);
				}
				break;
			case "jio":
				if ($registers[$reg] == 1) {
					jump($val, $input);
				}
				break;
			default:
				echo "wtf!!\n";
		}
		next($input);
	}

	return $registers;
}
