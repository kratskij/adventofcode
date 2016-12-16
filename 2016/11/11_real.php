<?php
require_once("Floor.php");
require_once("Building.php");
require_once("Item.php");
require_once("Elevator.php");

$buildings = [];

$floors = new FloorCollection([]);
$floors->add(1, new ItemCollection([new RTG("S"),new MicroChip("S"),new RTG("P"),new MicroChip("P")]));
$floors->add(2, new ItemCollection([new RTG("T"),new RTG("R"),new RTG("C"),new MicroChip("R"),new MicroChip("C")]));
$floors->add(3, new ItemCollection([new MicroChip("T")]));
$floors->add(4, new ItemCollection([]));

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
	#echo count($analyzedStructures)."\n";
	echo "Part $part: " . $tree->node()->getElevator()->getSteps() . "\n";
}
