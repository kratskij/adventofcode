<?php
require_once("Floor.php");
require_once("Building.php");
require_once("Item.php");
require_once("Elevator.php");

$floors = new FloorCollection([]);
$floors->add(1, new ItemCollection([new RTG("S"),new MicroChip("S"),new RTG("P"),new MicroChip("P")]));
$floors->add(2, new ItemCollection([new RTG("T"),new RTG("R"),new RTG("C"),new MicroChip("R"),new MicroChip("C")]));
$floors->add(3, new ItemCollection([new MicroChip("T")]));
$floors->add(4, new ItemCollection([]));
$building = new Building($floors);

//let's move items to top
$tree = new BuildingTree($building);
$endResults = $tree->moveItemsToTop();
$leaves = $tree->getLeaves();
foreach ($leaves as $leaf) {
	echo $leaf."\n\n";
}
var_dump($endResults);

echo "Part 1: " . $building->getElevator()->getSteps() . "\n";


function moveToTop($building)
{


}
