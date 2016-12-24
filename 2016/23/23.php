<?php

$test = false;

$file = ($test) ? "../test.txt" : "input";
$input = explode("\n", trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file)));

$regex = "/([a-z]+)\s([\da-d*)\s?([\-a-z0-9])/";

$values = ["a" => 12, "b" => 0, "c" => 0, "d" => 0];

$c = 0;
for ($i = 0; $i < count($input); $i++) {
	$c++;
	$row = $input[$i];
	$matches = explode(" ", $row);
	#if ($c % 100000 == 0) {
		#system("clear");
		echo $row."\n";
		echo implode(" ", array_keys($values))."\n";
		echo implode(" ", $values)."\n";
		usleep(100000);
	#}
	#var_dump($input);
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
			break;

		case "jnz":
		 	#x y jumps to an instruction y away (positive means forward; negative means backward), but only if x is not zero.
			$cmp = (is_numeric($matches[1])) ? (int)$matches[1] : $values[$matches[1]];
			$steps = (is_numeric($matches[2])) ? (int)$matches[2] : $values[$matches[2]];

			if ($cmp != 0)  {
				$i += $steps - 1;
				#echo $i."\n";
			}
			#echo $matches[0] . " " . $matches[1] . " " . $matches[2] . (string)($values[$matches[1]] != 0) . "\n";
			break;

		case "tgl":
			#x toggles the instruction x away (pointing at instructions like jnz does: positive means forward; negative means backward):
			$value = (is_numeric($matches[1]) ? (int)$matches[1] : $values[$matches[1]]);
			if ($value == 0) {
				break;
			}
			if (!isset($input[$i + $value])) {
				break;
			}
			$tglInstruction = $input[$i + $value];
			$instructionSplit = explode(" ", $tglInstruction);
			if (count($instructionSplit) == 2) {
				if ($instructionSplit[0] == "inc") {
					$instructionSplit[0] = "dec";
				} else {
					$instructionSplit[0] = "inc";
				}
			} else if (count($instructionSplit) == 3) {
				if ($instructionSplit[0] == "jnz") {
					$instructionSplit[0] = "cpy";
				} else {
					$instructionSplit[0] = "jnz";
				}
			}
			$input[$i + $value] = implode(" ", $instructionSplit);
			echo $input[$i + $value] . "\n";
			break;
		default:
			echo "WTF";

	}
}



var_dump($values);
