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
		return $this->_floors->top();
	}

	public function getFloors()
	{
		return $this->_floors;
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

	public function getStructure()
	{
		$ret = "";
		foreach ($this->_floors as $floor) {
			$ret .= $floor->getId() . ": " . $floor->getItems()->getStructure() .  ", Elevator at " . $this->_elevator->getPosition() . "\n";
		}
		return $ret;
	}

	public function __toString()
	{
		$items = [];
		foreach ($this->_floors as $floor) {
			foreach ($floor->getItems()->getItems() as $item) {
				$items[] = $item;
			}
		}

		foreach ($this->_elevator->getItems()->getItems() as $item) {
			$items[] = $item;
		}

		usort($items, function($a, $b) {
			return strcmp($a->getAtom() . $a->getType(), $b->getAtom() . $b->getType());
		});

		$str = "";
		$floor = $this->getTopFloor();
		while ($floor) {
			if ($floor == $this->_elevator->getFloor()) {
				// Since we're using bash colors, we must find the length added by color codes
				$elevatorItems = $this->_elevator->getItems()->getItems();
				$itemString = implode(" ", array_map(
					function($i) {
						return "[$i]";
					},
					$elevatorItems
				));

#				$padLengthExtra = count($this->_elevator->getItems()->getItems()) * 9;
				$str .= "E [ " . str_pad($itemString, 11, " ", STR_PAD_LEFT) . " ] ";

			} else {
				$str .= str_pad("", 18);
			}
			$str .= "F" . $floor->getId() . ":";

			foreach ($items as $item) {
				$itemString = "[" . (string)$item . "]";
				if (in_array($item, $floor->getItems()->getItems())) {
					$str .= ($item->highlighted()) ? " \e[32m$itemString\e[0m" : " $itemString";
				} else if ($this->_elevator->getFloor() == $floor && in_array($item, $this->_elevator->getItems()->getItems())) {
					$str .= " \e[32m$itemString\e[0m";
				} else {
					$str .= str_pad("", 1 + strlen($itemString));
				}
			}
			$str .= "\n";
			$floor = $floor->below();
		}
		return $str;
	}

	public function __clone()
	{
		$newFloors = new FloorCollection();
		foreach ($this->_floors as $floor) {
			$newFloors->add($floor->getId(), new ItemCollection($floor->getItems()->getItems()));
		}
		$newElevator = new Elevator($newFloors, $this->_elevator->getSteps());
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
	private $_parent;
	private static $_previousStates = [];

	public function __construct($building, $level = 0, BuildingTree $parent = null)
	{
		$this->_node = $building;
		$this->_level = $level;
		$this->_parent = $parent;
	}

	public function add($building)
	{
		$hash = md5((string)$building);
		if (isset(self::$_previousStates[$hash])) {
			return;
		}

		self::$_previousStates[$hash] = true;

		$this->_children[] = new BuildingTree($building, $this->_level + 1, $this);
	}

	public function node()
	{
		return $this->_node;
	}

	public function moveItems()
	{
		if ($this->_node->getLowestFloorWithItems() == $this->_node->getTopFloor()) {
			return [$this->_node];
		}

		$elevator = $this->_node->getElevator();
		$floor = $this->_node->getElevator()->getFloor();
		$loadableItems = $floor->getItems()->getItems();

		//let's move items down!
		if ($this->_node->getLowestFloorWithItems() != $floor) {
			foreach ($loadableItems as $a) {
				foreach ([Elevator::DOWN, Elevator::UP] as $direction) {
					try {
						$this->move(new ItemCollection([$a]), $direction);
					}
					catch (Exception $e) {
#						echo $e->getMessage() . "\n";
					}
				}
			}
		}

		//let's move items up!
		$usedCombos = [];
		foreach ($loadableItems as $a) {
			foreach ($loadableItems as $b) {
				if ($a != $b) {
					try {
						$items = new ItemCollection([$a, $b]);
						if (in_array((string)$items, $usedCombos)) {
							continue;
						}
						$usedCombos[] = (string)$items;
						$this->move($items, Elevator::UP);
					}
					catch (Exception $e) {
#						echo $e->getMessage()."\n";
					}
				}
			}
			try {
				$this->move(new ItemCollection([$a]), Elevator::UP);
			}
			catch (Exception $e) {
#				echo $e->getMessage();
			}
		}
	}

	public function getChildren()
	{
		return $this->_children;
	}

	public function parent()
	{
		return $this->_parent;
	}

	private function move(ItemCollection $items, $direction)
	{
		$copy = clone $this->_node;
		try {
			$copy->getElevator()->load($items);
			$copy->getElevator()->ride($direction);
			$copy->getElevator()->unload();
			$this->add($copy);
		}
		catch (Exception $e) {
			#echo "ERROR: " . $e->getMessage() . "\n";
		}
	}

	public function getDepth()
	{
		return $this->_level;
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
