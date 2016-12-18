<?php

$input = "^^^^......^...^..^....^^^.^^^.^.^^^^^^..^...^^...^^^.^^....^..^^^.^.^^...^.^...^^.^^^.^^^^.^^.^..^.^";
#$input = ".^^.^.^^^^";

$world = [$input];
$len = strlen($input);

$traps = [
	"^^.",
	".^^",
	"^..",
	"..^",
];
for ($i = 1; $i < 400000; $i++) {
	$world[$i] = "";
	for ($j = 0; $j < $len; $j++) {
		$world[$i] .= in_array(
			implode("",[
				($j == 0) ? "." : $world[$i - 1][$j - 1],
				$world[$i-1][$j],
				($j == $len - 1) ? "." : $world[$i - 1][$j + 1],
			]),
			$traps
		) ? "^" : ".";
	}
}
#echo implode("\n", $world) . "\n";
echo "Part 1: " . substr_count(implode("", array_slice($world, 0, 40)), ".") . "\n";
echo "Part 2: " . substr_count(implode("", $world), ".") . "\n";
