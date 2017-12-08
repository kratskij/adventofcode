<?php

$test = false;

$file = ($test) ? "test" : "input";
$input = explode("\n", file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file));

$max = 0;
echo "Part 1: " . max(compute([], $input, $max)) . "\n";
echo "Part 2: $max\n";

function compute($registers, $input, &$max) {
	while ($row = current($input)) {
		$parts = explode(" ", $row);
        list($register, $cmd, $val, $if, $condReg, $condComp, $condValue) = $parts;
        if (!isset($registers[$condReg])) {
            $registers[$condReg] = 0;
        }
        if (!isset($registers[$register])) {
            $registers[$register] = 0;
        }
        if (
            ($condComp == ">" && !($registers[$condReg] > $condValue)) ||
            ($condComp == "<" && !($registers[$condReg] < $condValue)) ||
            ($condComp == ">=" && !($registers[$condReg] >= $condValue)) ||
            ($condComp == "<=" && !($registers[$condReg] <= $condValue)) ||
            ($condComp == "==" && !($registers[$condReg] == $condValue)) ||
            ($condComp == "!=" && !($registers[$condReg] != $condValue))
        ) {
            next($input);
            continue;
        }

		switch ($cmd) {
			case "inc":
				$registers[$register] += $val;
				break;
			case "dec":
				$registers[$register] -= $val;
				break;
			default:
				echo "wtf!!\n";
		}
        $max = max($registers[$register], $max);

		next($input);
	}

	return $registers;
}
