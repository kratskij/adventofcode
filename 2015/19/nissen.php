<?php

$input = explode("\n", file_get_contents("input"));
$molecule = array_pop($input);
array_pop($input);
$replacements = [];
foreach ($input as $row) {
	list($from, $to) = split(" => ", $row);
	$replacements[] = ["from" => $from, "to" => $to];
}

#part 1
$values = [];
$repl = $replacements;
foreach ($repl as $r) {
	$parts = explode($r["from"], $molecule);
	for ($i = 1; $i < count($parts); $i++) {
		$values[] = join($r["from"], array_slice($parts, 0, $i)) .
		$r["to"] .
		join($r["from"], array_slice($parts, $i));
	}
}
#var_dump($values);
echo "Part 1: " . count(array_unique($values)) . "\n";


$mol = $molecule;
$c = 0;
$skip = [];

while($mol != "e") {
	$pMol = $mol;
	foreach ($replacements as $k => $r) {
		if (isset($skip[$mol][$k])) {
			continue;
		} else {
			$skip[$mol][$k] = true;
		}
		$mol = str_replace($r["to"], $r["from"], $mol, $count);
		$c += $count;
	}
	if ($pMol == $mol) {
		$c = 0;
		$mol = $molecule;
	}
}
echo "Part 2: " . $c . "\n";