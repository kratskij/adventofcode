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
		$items = [];
		foreach ($this->_floors as $floor) {
			foreach ($floor->getItems()->getItems() as $item) {
				$items[] = $item;
			}
		}

		foreach ($this->_elevator->getItems()->getItems() as $item) {
			$items[] = $item;
		}

		usort($items, "strcmp");
		$str = "";
		$floor = $this->getTopFloor();
		while ($floor) {
			#var_dump($floor);
		#foreach ($this->_floors as $floor) {
			if ($floor == $this->_elevator->getFloor()) {
				$str .= "E [" . str_pad((string)$this->_elevator->getItems(), 10) . "]  ";
			} else {
				$str .= str_pad("", 16);
			}
			$str .= "F" . $floor->getId() . ":";
			foreach ($items as $item) {
				if (in_array($item, $floor->getItems()->getItems())) {
					$str .= " [$item]";
				} else {
					$str .= "     ";
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
			return;
		}

		self::$_previousStates[$hash] = true;
		$str1 = explode("\n", (string)$this->_node);
		$str2 = explode("\n", (string)$building);
		$str = "";
		foreach ($str1 as $k => $r1) {
			$str .= $r1 ."   ->   " . $str2[$k] . "\n";
		}
		#echo "Adding state (depth {$this->_level}): \n$str\n"; sleep(1);

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

		$elevator = $this->_node->getElevator();
		$floor = $this->_node->getElevator()->getFloor();
		$loadableItems = $floor->getItems()->getItems();

		if ($this->_node->getLowestFloorWithItems() == $floor) {
			//let's move items up!
			foreach ($loadableItems as $a) {
				foreach ($loadableItems as $b) {
					if ($a != $b) {
						try {
							$this->moveItems(new ItemCollection([$a, $b]), Elevator::UP);
						}
						catch (Exception $e) {
							echo $e->getMessage()."\n";
						}
					}
				}
				$this->moveItems(new ItemCollection([$a]), Elevator::UP);
			}
		} else {
			foreach ($loadableItems as $a) {
				foreach ([Elevator::DOWN, Elevator::UP] as $direction) {
					$this->moveItems(new ItemCollection([$a]), $direction);
				}
			}
		}

		$valid = [];
		foreach($this->_children as $child) {
			$v = $child->moveItemsToTop();
			if ($v !== false) {
				if (is_array($v) && !empty($v)) {
					$valid = array_merge($valid, $v);
				}
			}
		}

		return $valid;
	}

	public function getLeaves()
	{
		if (!$this->_children) {
			return [$this->_node];
		}

		$ret = [];
		foreach ($this->_children as $child) {
			$ret = array_merge($ret, $child->getLeaves());
		}
		return $ret;
	}

	private function moveItems(ItemCollection $items, $direction)
	{
		$copy = clone $this->_node;
		try {
			$printer = function($building, $prefix) {
				$lines = "";
				foreach (explode("\n", (string)$building) as $line) {
					$lines .= $prefix . $line . "\n";
				}
				echo $lines."\n";
			};
			$prefix = "";
			for ($i = 0; $i < $this->_level - 1; $i++) {
				$prefix .= "  ";
			}
			$printer($copy, $prefix);
			$copy->getElevator()->load($items);
			$printer($copy, $prefix . "  ");
			$copy->getElevator()->ride($direction);
			$printer($copy, $prefix . "  ");
			$copy->getElevator()->unload();
			$printer($copy, $prefix . "  ");
			$this->add($copy);
		}
		catch (Exception $e) {
			echo "ERROR: " . $e->getMessage() . "\n";
		}
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
