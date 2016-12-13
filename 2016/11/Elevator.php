<?php

class Elevator
{

	const UP = 1;
	const DOWN = 2;

	private $_items;
	private $_position = 0;
	private $_availableFloors;
	private $_steps = 0;

	public function __construct(FloorCollection $availableFloors)
	{
		$this->_availableFloors = $availableFloors;
		$this->_items = new ItemCollection([]);
	}

	public function load(ItemCollection $items)
	{
		if (count($this->_items->getItems()) + count($items->getItems()) > 2) {
			throw new Exception("Elevator overloaded! Can not load " . $items);
		}
		$this->_items = $items;
		$this->getFloor()->removeItems($items);
	}

	public function ride($direction)
	{
		switch($direction) {
			case self::UP:
				$this->setPosition($this->_position + 1);
				$this->_steps += 1;
				break;
			case self::DOWN:
				$this->setPosition($this->_position - 1);
				$this->_steps += 1;
				break;
		}
	}

	public function setPosition($pos)
	{
		$set = false;
		foreach ($this->_availableFloors as $key => $floor) {
			if ($key == $pos) {
				$this->_position = $pos;
				$set = true;
				break;
			}
		}
		if (!$set) {
			throw new Exception("Floor $pos does not exist!");
		}
	}

	public function getPosition()
	{
		return $this->_position;
	}

	public function getFloor()
	{
		foreach ($this->_availableFloors as $key => $floor) {
			if ($key == $this->_position) {
				return $floor;
			}
		}
		return false;
	}

	public function getSteps()
	{
		return $this->_steps;
	}


	public function unload()
	{
		#echo "Unloading " . $this->_items . " at floor " . $this->getFloor()->getId() . "\n";
		$this->getFloor()->addItems($this->_items);
		$this->_items = new ItemCollection([]);
	}

	public function getItems()
	{
		return $this->_items;
	}

}
