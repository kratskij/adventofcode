<?php

$test = false;

$file = ($test) ? "../test.txt" : "input.txt";
$input = explode("\n", trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file)));

$regex = "/([a-z]+)\s([\da-d*)\s?([\-a-z0-9])/";

$values = ["a" => 0, "b" => 0, "c" => 1, "d" => 0];

for ($i = 0; $i < count($input); $i++) {
	$row = $input[$i];
	$matches = explode(" ", $row);
	#echo $row."\n";
	#echo md5(json_encode($values))."\n";
	switch ($matches[0]) {
		case "cpy":
			$values[$matches[2]] = is_numeric($matches[1]) ? (int)$matches[1] : $values[$matches[1]];
			#echo $matches[0] . " " . $matches[1] . " " . $matches[2] . "\n";
			break;
		case "inc":
		 	#x increases the value of register x by one.
			$values[$matches[1]]++;
			#echo $matches[0] . " " . $matches[1] . "\n";
			break;
		case "dec":
		 	#x decreases the value of register x by one.
			$values[$matches[1]]--;

			#echo $matches[0] . " " . $matches[1] . "\n";


			break;
		case "jnz":
		 	#x y jumps to an instruction y away (positive means forward; negative means backward), but only if x is not zero.
			$cmp = (is_numeric($matches[1])) ? (int)$matches[1] : $values[$matches[1]];
			if ($cmp != 0)  {
				$i += (int)$matches[2] - 1;
			}
			#echo $matches[0] . " " . $matches[1] . " " . $matches[2] . (string)($values[$matches[1]] != 0) . "\n";
			break;
		default:
			echo "WTF";

	}
}



var_dump($values);
