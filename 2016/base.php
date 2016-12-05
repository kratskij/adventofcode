<?php

$test = true;

$file = ($test) ? "../test.txt" : "input.txt";
$input = explode("\n", trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file)));

$regex = "//";

$values = [];

foreach ($input as $row) {
	preg_match($regex, $row, $matches);
	array_shift($matches); # remove first match (which is the whole matched string)

	var_dump($matches);
	#$values[] = $matches[0];


	#while ($prop = array_shift($matches)) {
	#	$values[] = (int)array_shift($matches);
	#}


	#$id = array_shift($matches);

	#list($one, $two, $three, $foru) = $matches;
	#var_dump($one, $two, $three, $four);
}



#var_dump($value);
