<?php

class ItemCollection implements Countable, Iterator
{
	private $_items = [Item::GENERATOR => [], Item::MICROCHIP => []];
	private $_position = 0;

	public function __construct($items)
	{
		foreach ($items as $item)
		{
			$this->_items[$item->getType()][$item->getAtom()] = $item;
		}
		$this->analyze();
	}


	public function rewind()
	{
		$this->_position = 0;
	}

	public function current()
	{
		return $this->_items[$this->_position];
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
		return isset($this->_items[$this->_position]);
	}

	public function count() {
	    return count($this->_items);
	}

	public function add(ItemCollection $items)
	{
		foreach ($items->getItems() as $item) {
			$this->_items[$item->getType()][$item->getAtom()] = $item;
		}
		$this->analyze();
	}

	public function remove(ItemCollection $items)
	{
		foreach ($items->getItems() as $item) {
			unset($this->_items[$item->getType()][$item->getAtom()]);
		}
		$this->analyze();
	}

	private function analyze()
	{
		foreach ($this->_items[Item::MICROCHIP] as $chip) {
			if (count($this->_items[Item::GENERATOR]) > 0 && !isset($this->_items[Item::GENERATOR][$chip->getAtom()])) {
				throw new Exception(
					"Oh noes, chip " . $chip . " is dead!" .
					" (Collection: " . implode(" ", $this->_items[Item::GENERATOR]) . " " . implode(" ", $this->_items[Item::MICROCHIP]) . ")");
			}
		}
	}

	public function getStructure()
	{
		return count($this->getPairs()) . "pairs, " .
			count($this->getSingles(Item::GENERATOR)) . " generators, " .
			count($this->getSingles(Item::MICROCHIP)) . " microchips";
	}

	private function getPairs()
	{
		$keys = array_intersect(array_keys($this->_items[Item::GENERATOR]), array_keys($this->_items[Item::MICROCHIP]));

		$ret = [];
		foreach ($keys as $key) {
			$ret[$key] = new ItemCollection([$this->_items[Item::GENERATOR][$key], $this->_items[Item::MICROCHIP][$key]]);
		}

		return $ret;
	}

	private function getSingles($type)
	{
		$keys = array_diff(array_keys($this->_items[$type]), array_keys($this->_items[($type == Item::GENERATOR) ? Item::MICROCHIP : Item::GENERATOR]));

		$ret = [];
		foreach ($keys as $key) {
			$ret[$key] = new ItemCollection([$this->_items[$type][$key]]);
		}

		return $ret;
	}

	public function getItems()
	{
		$items = [];
		foreach ($this->_items[Item::GENERATOR] as $item) {
			$items[] = $item;
		}
		foreach ($this->_items[Item::MICROCHIP] as $item) {
			$items[] = $item;
		}
		return $items;
	}

	public function __toString()
	{
		sort($this->_items[Item::GENERATOR]);
		sort($this->_items[Item::MICROCHIP]);
		$ret = "";
		foreach ($this->_items[Item::GENERATOR] as $item) {
			$ret .= "  " . $item;
		}
		foreach ($this->_items[Item::MICROCHIP] as $item) {
			$ret .= "  " . $item;
		}

		return $ret;
	}
}

abstract class Item
{
	protected $_atom;
	protected $_type;

	const MICROCHIP = "M";
	const GENERATOR = "G";

	public function getAtom()
	{
		return $this->_atom;
	}
	public function getType()
	{
		return $this->_type;
	}

	public function __toString()
	{
		return $this->_atom . $this->_type;;
	}
}

class RTG extends Item
{
	public function __construct($atom)
	{
		$this->_atom = $atom;
		$this->_type = self::GENERATOR;
	}
}
class MicroChip extends Item
{
	public function __construct($atom)
	{
		$this->_atom = $atom;
		$this->_type = self::MICROCHIP;
	}
}
