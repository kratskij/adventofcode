<?php

$input = explode("\n", file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "nissen.txt"));

$values = [];
foreach ($input as $row => $line) {
	$values[$row] = [];
	$line = str_split($line);
	foreach ($line as $col => $char) {
		$values[$row][$col] = (bool)($char == "#");
	}
}

$cornersOn = true; # true = enable part 2
#part 2:
if ($cornersOn) {
	$values[0][0] = true;
	$values[0][count($values)-1] = true;
	$values[count($values)-1][0] = true;
	$values[count($values)-1][count($values)-1] = true;
}

for ($i = 1; $i <= 100; $i++) {
	$count = 0;
	foreach ($values as $line => $row) {
		$newGrid[$line] = [];
		foreach ($row as $col => $char) {
			if ($cornersOn) {
				if (
					($line == 0 || $line == count($values)-1) &&
					($col == 0 || $col == count($values)-1)
				) {
					$newGrid[$line][$col] = true;
					continue;
				}
			}
			$nb = 0;
			for ($y = $line-1; $y <= $line+1; $y++) {
				for ($x = $col-1; $x <= $col+1; $x++) {
					if ($y == $line && $x == $col) { continue; }
					if (isset($values[$y][$x]) && $values[$y][$x]) {
						$nb++;
					}
				}
			}

			if ($char) {
				$newGrid[$line][$col] = (bool)($nb == 2 || $nb == 3);
			} else {
				$newGrid[$line][$col] = (bool)($nb == 3);
			}
		}
		$count += count(array_filter($newGrid[$line]));
	}

	$values = $newGrid;
	echo "after $i runs: " . $count . "\n"; 
}

function vPrint($values) {
	foreach ($values as $row) {
		foreach ($row as $char) {
			echo ($char) ? "#" : ".";
		}
		echo "\n";
	}
	echo "\n";
}