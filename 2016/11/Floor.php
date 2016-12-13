<?php

class Floor
{
	private $_id;
	private $_items;
	private $_siblings;

	public function __construct($id, ItemCollection $items, FloorCollection $siblings)
	{
		$this->_id = $id;
		$this->_items = $items;
		$this->_siblings = $siblings;
	}

	public function getId()
	{
		return $this->_id;
	}

	public function getItems()
	{
		return $this->_items;
	}

	public function addItems(ItemCollection $items)
	{
		$this->_items->add($items);
	}

	public function removeItems(ItemCollection $items)
	{
		$this->_items->remove($items);
	}

	public function __toString()
	{
		return "F" . $this->getId() . ":" . $this->_items;
	}

	public function getDoubleCombinations()
	{
		$ret = [];
		foreach ($this->_floors as $a) {
			foreach ($this->_floors as $b) {
				$ret[] = [$a, $b];
			}
		}

		return $ret;
	}

	public function above()
	{
		return $this->_siblings->above($this);
	}

	public function below()
	{
		return $this->_siblings->below($this);
	}
}

class FloorCollection implements Iterator
{
	private $_position = 0;
	private $_floors = [];


	public function add($floorId, ItemCollection $items)
	{
		$this->_floors[] = new Floor($floorId, $items, $this);
	}

	public function rewind()
	{
		$this->_position = 0;
	}

	public function current()
	{
		return $this->_floors[$this->_position];
	}

	public function key()
	{
		return $this->_position;
	}

	public function next()
	{
		++$this->_position;
	}

	public function valid()
	{
		return isset($this->_floors[$this->_position]);
	}

	public function getFirst()
	{
		return $this->_floors[0];
	}

	public function print($elevatorFloor)
	{
		$ret = "";
		foreach (array_reverse($this->_floors) as $floor) {
			$ret .= ($floor == $elevatorFloor ? "* " : "  ") . $floor . "\n";
		}
		return $ret;
	}

	public function last()
	{
		return current(array_slice($this->_floors, -1));
	}

	private function get($index)
	{
		if (isset($this->_floors[$index])) {
			return $this->_floors[$index];
		}
		return false;
	}

	public function above()
	{
		return $this->get(array_search($floor, $this->_floors) + 1);
	}


	public function below()
	{
		return $this->get(array_search($floor, $this->_floors) + 1);
	}
}
