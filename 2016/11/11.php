<?php
require_once("Floor.php");
require_once("Building.php");
require_once("Item.php");
require_once("Elevator.php");

$floors = new FloorCollection([]);
$file = "input.txt";
$file = "input_thomas.txt";

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
$buildings[2]->getFloors()->bottom()->addItems(new ItemCollection([new RTG("elerium"),new MicroChip("elerium"),new RTG("dilithium"),new MicroChip("dilithium")]));

foreach (range(1,2) as $part) {
	$queue = [new BuildingTree($buildings[$part])];
	$analyzed = 0;
	$maxDepth = 0;

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
		if ($animate) {
			if ($tree->getDepth() > $maxDepth) {
				$prefix = "Part: $part\n";
				$maxDepth = $tree->getDepth();
				system("clear");
				echo "Part: $part\nSteps: " . $tree->getDepth() . "\n" . str_pad("", $maxDepth, "|");
			}
		}
	}

	if ($animate) {
		$branch = [$tree->node()];
		$pTree = $tree;
		while ($p = $pTree->parent()) {
			array_unshift($branch, $p->node());
			$pTree = $p;
		}
		$prevBuilding = false;
		foreach ($branch as $key => $building) {
			if ($prevBuilding) {
				animate($prevBuilding, $building, $key - 1, $part, 350000, $maxDepth + 1 );
			}
			$prevBuilding = $building;
		}
	} else {
		#echo count($analyzedStructures)."\n";
		echo "Part $part: " . $tree->node()->getElevator()->getSteps() . "\n";
	}
}

function animate(Building $building1, Building $building2, $initSteps, $part, $delay, $maxDepth)
{
	$items1 = $building1->getElevator()->getFloor()->getItems()->getItems();
	$items2 = $building2->getElevator()->getFloor()->getItems()->getItems();
	$items2Ids = array_map(function($i) { return (string)$i; }, $items2);
	$movedItems = array_filter(
		$items1,
		function($item) use ($items2Ids) {
			if (in_array((string)$item, $items2Ids)) {
				$item->highlight();
				return true;
			}
			return false;
		}
	);
	$direction = ($building1->getElevator()->getFloor()->getId() > $building2->getElevator()->getFloor()->getId()) ? Elevator::DOWN : Elevator::UP;

	#printer($building1, $initSteps, $part, $delay, $maxDepth);
	$building1->getElevator()->load(new ItemCollection($movedItems));
	printer($building1, $initSteps, $part, $delay, $maxDepth);
	$building1->getElevator()->ride($direction);
	printer($building1, $initSteps + 1, $part, $delay, $maxDepth);
	$building1->getElevator()->unload();

	array_map(
		function($item) {
			$item->lolight();
		},
		$movedItems
	);
	printer($building1, $initSteps + 1, $part, $delay, $maxDepth);
}

function printer(Building $building, $steps, $part, $delay, $maxDepth)
{
	system("clear");
	echo "Part: $part\nSteps: $steps/$maxDepth\n$building\n";
	usleep($delay);
}
