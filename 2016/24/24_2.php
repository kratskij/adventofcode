<?php
require_once '../skeletons/InputReader.php';
require_once '../skeletons/BFS.php';

$config = [
	$test => false,
	$animate => false,
];

$ir = new InputReader($config["test"]);

/*
 * BFS
 */
$grid = $ir->grid(["." => true, "#" => false]);
$numberPositions = [];
foreach ($grid as $y => $row) {
	foreach ($row as $x => $val) {
		if (is_numeric($val)) {
			$numberPositions[] = [$y, $x, $val];
		}
	}
}
$shortestPaths = [];
foreach ($numberPositions as $pos1) {
	foreach ($numberPositions as $pos2) {
		$bfs = new BFS($grid);
		$result = $bfs->shortestPath($pos1[0], $pos1[1], $pos2[0], $pos2[1]);
		$shortestPaths[$pos1[2]][$result["value"]->getNode()] = $result["value"]->distance();
	}
}

$matches = $ir->regex('//');
