<?php

$input = explode("\n", file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "nissen.txt"));

$tape = [
	"children" => 3,
	"cats" => 7,
	"samoyeds" => 2,
	"pomeranians" => 3,
	"akitas" => 0,
	"vizslas" => 0,
	"goldfish" => 5,
	"trees" => 3,
	"cars" => 2,
	"perfumes" => 1,
];

$regex = "/\w+ (\d+): (\w+): (\d+), (\w+): (\d+), (\w+): (\d+)/";

$sues = [];

foreach ($input as $row) {
	preg_match($regex, $row, $matches);
	array_shift($matches); # remove first match (which is the whole matches string)

	$id = array_shift($matches);
	$sues[$id] = [];
	while ($prop = array_shift($matches)) {
		$sues[$id][$prop] = (int)array_shift($matches);
	}
}

$sues = array_keys(
	array_filter(
		$sues,
		function($sue) use ($tape) {
			foreach ($sue as $prop => $num) {
				# part 2
				if (in_array($prop, ["cats", "trees"])) {
					if ($num <= $tape[$prop]) return false;
				} else if (in_array($prop, ["pomeranians", "goldfish"])) {
					if ($num >= $tape[$prop]) return false;
				} else

				#part 1
				if ($tape[$prop] != $num) return false;
			}
			return true;
		}
	)
);

var_dump($sues);