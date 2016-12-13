<?php

class Building
{
	private $_floors;
	private $_elevator;

	public function __construct(FloorCollection $floors)
	{
		$this->_floors = $floors;
		$this->_elevator = new Elevator($this->_floors);
	}

	public function getFloorAbove(Floor $below)
	{
		foreach ($this->_floors as $floor) {
			if ($next) {
				return $this->_floors->next();
			}
		}

		return false;
	}

	public function getFloorBelow(Floor $above)
	{
		foreach ($this->_floors as $floor) {
			if ($floor == $above) {
				return $this->_floors->previuos();
			}
		}

		return false;
	}

	public function getTopFloor()
	{
		return $this->_floors->last();
	}

	public function getLowestFloorWithItems()
	{
		foreach ($this->_floors as $floor) {
			if (count($floor->getItems()->getItems())) {
				return $floor;
			}
		}
	}

	public function getElevator()
	{
		return $this->_elevator;
	}

	public function __toString()
	{
		return $this->_floors->print($this->_elevator->getFloor());
	}

	public function __clone()
	{
		$newFloors = new FloorCollection();
		foreach ($this->_floors as $floor) {
			$items = [];
			foreach ($floor->getItems()->getItems() as $item) {
				$items[] = $item;
			}
			$newFloors->add($floor->getId(), new ItemCollection($items));
		}
		$newElevator = new Elevator($newFloors);
		$newElevator->setPosition($this->_elevator->getPosition());

		$this->_floors = $newFloors;
		$this->_elevator = $newElevator;
	}
}

class BuildingTree
{
	private $_level;
	private $_node;
	private $_children = [];
	private static $_previousStates = [];

	public function __construct($building, $level = 1)
	{
		$this->_node = $building;
		$this->_level = $level;
	}

	public function add($building)
	{
		$hash = md5((string)$building);
		if (isset(self::$_previousStates[$hash])) {
			echo "already found: $hash\n";
			return;
		}
		echo "generating $hash\n";
		self::$_previousStates[$hash] = true;

		$this->_children[] = new BuildingTree($building, $this->_level + 1);
	}

	public function node()
	{
		return $this->_node();
	}

	public function moveItemsToTop()
	{

		if ($this->_node->getLowestFloorWithItems() == $this->_node->getTopFloor()) {
			return [$this->_node];
		}

		#echo $this->_node."\n";
		$elevator = $this->_node->getElevator();
		$floor = $this->_node->getElevator()->getFloor();
		$loadableItems = $floor->getItems()->getItems();
		#var_dump($loadableItems);
		if ($this->_node->getLowestFloorWithItems() == $floor) {
			//let's move items up!
			foreach ($loadableItems as $a) {
				foreach ($loadableItems as $b) {
					if ($a != $b) {
						$copy = clone $this->_node;

						try {
							$copy->getElevator()->load(new ItemCollection([$a, $b]));
							$copy->getElevator()->ride(Elevator::UP);
							$copy->getElevator()->unload();
							$this->add($copy);
						}
						catch (Exception $e) {
							#echo "ERROR: " . $e->getMessage() . "\n";
						}
					}
				}
			}
		} else {
			foreach ($loadableItems as $a) {
				$copy = clone $this->_node;
				try {
					$copy->getElevator()->load(new ItemCollection([$a]));
					$copy->getElevator()->ride(Elevator::DOWN);
					$copy->getElevator()->unload();
					$this->add($copy);
				}
				catch (Exception $e) {
					#echo "ERROR: " . $e->getMessage() . "\n";
				}
			}
		}
		echo $this->_node."\n";

		$valid = [];
		foreach($this->_children as $child) {
			$v = $child->moveItemsToTop();
			if ($v !== false) {
				if (is_array($v)) {
					$valid = array_merge($valid, $v);
				}
			}
		}

		return $valid;
	}

	public function __toString()
	{
		$ret = "";
		$prefix = "";
		for ($i = 0; $i < $this->_level; $i++) {
			$prefix .= "  ";
		}
		$lines = $this->_node;
		foreach (explode("\n", $lines) as $line) {
			$ret .= $prefix . $line . "\n";
		}

		foreach ($this->_children as $child) {
			$ret .= $child . "\n";
		}

		return $ret;
	}
}
