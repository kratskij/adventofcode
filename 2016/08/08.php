<?php

$test = false;

$file = ($test) ? "../test.txt" : "input.txt";
$input = explode("\n", trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file)));

$display = [];
foreach (range(0, 5) as $y) {
	foreach (range(0, 49) as $x) {
		$display[$y][$x] = false;
	}
}
$c = 0;
foreach ($input as $row) {
	echo $row."\n";
	if (substr($row, 0, 4) == "rect") {
		preg_match("/rect (\d+)x(\d+)/", $row, $matches);
		array_shift($matches); # remove first match (which is the whole matched string)
		foreach (range(0, $matches[0] - 1) as $x) {
			foreach (range(0, $matches[1] - 1) as $y) {
				if ($display[$y][$x] === false) {
					$c++;
				}
				$display[$y][$x] = true;
			}
		}
	} else if (substr($row, 7, 3) == "col") {
		preg_match("/rotate column x=(\d+) by (\d+)/", $row, $matches);
		array_shift($matches); # remove first match (which is the whole matched string)
		$copy = [];
		foreach(range(0, count($display) - 1) as $y) {
			$copy[$y] = $display[$y][$matches[0]];
			$prevPos = $y - $matches[1];
			if ($prevPos < 0) { $prevPos += count($display); }
			$prev = (isset($copy[$prevPos])) ? $copy[$prevPos] : $display[$prevPos][$matches[0]];
			$display[$y][$matches[0]] = $prev;
		}
	} else if (substr($row, 7, 3) == "row") {
		preg_match("/rotate row y=(\d+) by (\d+)/", $row, $matches);
		array_shift($matches); # remove first match (which is the whole matched string)

		for ($i = 0; $i < $matches[1]; $i++) {
			array_unshift($display[$matches[0]], array_pop($display[$matches[0]]));
		}
	} else {
		echo "ERROR: " . $row;
	}
	display($display);
	echo "\n";
}
echo "realcount: " . $c."\n";

function display($d) {
	$count = 0;
	foreach ($d as $row) {
		echo implode("",
			array_map(
				function($v) {
					if ($v) {
						return '#';
					}
					return ".";
				},
				$row
			)
		) . "\n";
		$count += count(array_filter($row));
	}

	echo "Count: " . $count;
}
