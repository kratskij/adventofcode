<?php
require_once("Floor.php");
require_once("Building.php");
require_once("Item.php");
require_once("Elevator.php");

$floors = new FloorCollection([]);
$file = "input.txt";
#$file = "input_thomas.txt";

$input = explode("\n", file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file));
$animate = false;

foreach ($input as $linenumber => $line) {
	if (empty($line)) {
		continue;
	}
	$items = [];
	$words = explode(" ", $line);

	foreach ($words as $k => $word) {
		if ($word != "a") {
			continue;
		}
		$atom = explode("-", $words[$k + 1])[0];
		$type = str_replace([",", "."], "", $words[$k + 2]);
		switch($type) {
			case "microchip":
				$items[] = new MicroChip($atom);
				break;
			case "generator":
				$items[] = new RTG($atom);
				break;
			default:
				throw new Exception("WHAT IS A " . $type . "!?");
		}
	}
	$floors->add($linenumber + 1, new ItemCollection($items));
}
$buildings = [];

$buildings[1] = new Building($floors);
$buildings[2] = clone $buildings[1];
$buildings[2]->getFloors()->bottom()->addItems(new ItemCollection([new RTG("E"),new MicroChip("E"),new RTG("D"),new MicroChip("D")]));

foreach (range(1,2) as $part) {
	$queue = [new BuildingTree($buildings[$part])];
	$analyzed = 0;

	$analyzedStructures = [];
	while ($queue) {
		$analyzed++;
		$tree = array_shift($queue);

		$structure = $tree->node()->getStructure();
		$hash = md5($structure);
		if (isset($analyzedStructures[$hash])) {
			continue;
		}
		$analyzedStructures[$hash] = true;
		if ($tree->node()->getLowestFloorWithItems()->getId() == 4) {
			break;
		}
		$tree->moveItems();
		$queue = array_merge($queue, $tree->getChildren());
	}

	if ($animate) {
		$branch = [];
		$pTree = $tree;
		while ($p = $pTree->parent()) {
			array_unshift($branch, $p->node());
			$pTree = $p;
		}
		foreach ($branch as $key => $branch) {
			system("clear");
			echo "steps: " . ($key + 1) . "\n$branch\n";
			sleep(1);
		}
	}
	#echo count($analyzedStructures)."\n";
	echo "Part $part: " . $tree->node()->getElevator()->getSteps() . "\n";
}
