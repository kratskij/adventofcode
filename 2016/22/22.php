<?php

$test = false;

$file = ($test) ? "../test.txt" : "input";
$input = explode("\n", trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file)));

$regex = '/\w+\-x(\d+)\-y(\d+)\s+(\d+)T\s+(\d+)T\s+(\d+)T\s+(\d+)%/';

$nodes = [];

$keyPositions = [];

foreach ($input as $row) {
	preg_match($regex, $row, $matches);
	array_shift($matches); # remove first match (which is the whole matched string)
	$matches = array_map("intval", $matches);
	list($x, $y, $size, $used, $avail, $usePct) = $matches;
	$nodes[$y][$x] = new Node($x, $y, $size, $used, $avail, $usePct);
	if ($used == 0) {
		$keyPositions["emptyAtStart"] = [$x, $y];
	}
}
$nodes[0][max(array_keys($nodes[0]))]->setAsGoalNode();

$keyPositions["goalDataAtStart"] = [0, max(array_keys($nodes[0]))];
$keyPositions["end"] = [0,0];

$depths = [
	"0" => [$nodes]
];

$depth = $i = 0;

$previousStates = [];

const SEARCH_FOR_GOAL = 0;
const MOVE_GOAL_TO_END = 1;

$searchStatus = SEARCH_FOR_GOAL;

while($gridQueue = array_shift($depths)) {
	usort($gridQueue, function($a, $b) use ($nullStart) {
		foreach ($a as $y => $row) {
			foreach ($row as $x => $node) {
				if ($node->getUsed() == 0) {
					$aDist = abs($nullStart[0] - $x) + abs($nullStart[1] - $y);
					break 2;
				}
			}
		}
		foreach ($b as $y => $row) {
			foreach ($row as $x => $node) {
				if ($node->getUsed() == 0) {
					$bDist = abs($nullStart[0] - $x) + abs($nullStart[1] - $y);
					break 2;
				}
			}
		}
		return $bDist - $aDist;
	});
	$gridQueue = array_slice($gridQueue, 0, 100);

	while($grid = array_shift($gridQueue)) {
		if ($i % 100 == 0) {
			echo "\$i: $i, grid queue count: " . count($gridQueue). ", depth: " . $depth . "\n";
			printGrid($grid);
		}
		foreach ($grid as $y => $row) {
			foreach ($row as $x => $node) {
				if (!$node->getUsed()) { //can't move empty data

					//BUT! we can find it's neighbors!
					foreach (range(-1,1) as $ty) {
						if (!isset($grid[$y+$ty])) { continue; }
						foreach (range(-1,1) as $tx) {
							if ($tx != 0 && $ty != 0) { continue; }
							if ($tx == 0 && $ty == 0) { continue; }
							if (!isset($grid[$y+$ty][$x+$tx])) { continue; }
							$candidates = findAdjacentMatches($grid, $grid[$y+$ty][$x+$tx]);
							foreach ($candidates as $toNode) {
								#echo $node . "|" . $toNode."\n";
								$newGrid = cloneGrid($grid);
								$newGrid[$y+$ty][$x+$tx]->moveData($newGrid[$toNode->getY()][$toNode->getX()]);

								$hash = md5(json_encode($newGrid));
								if (isset($previousStates[$hash])) {
									continue;
								} else {
									$previousStates[$hash] = true;
								}

								$depths[(string)($depth + 1)][] = $newGrid;
							}
						}
					}
					continue;
				}
				/*
				$candidates = findAdjacentMatches($grid, $node);
				foreach ($candidates as $toNode) {
					#echo $node . "|" . $toNode."\n";
					$newGrid = cloneGrid($grid);
					$newGrid[$y][$x]->moveData($newGrid[$toNode->getY()][$toNode->getX()]);

					$hash = md5(json_encode($newGrid));
					if (isset($previousStates[$hash])) {
						continue;
					} else {
						$previousStates[$hash] = true;
					}

					$depths[(string)($depth + 1)][] = $newGrid;
				}*/
			}
		}
		if ($depth == 0) {
			echo "Part 1: " . count($gridQueue) . "\n";
		}
		$i++;
	}
	$depth++;
}

function cloneGrid(&$old)
{
	$new = [];
	foreach ($old as $y => $row) {
		foreach ($row as $x => $node) {
			$new[$y][$x] = clone $node;
		}
	}
	return $new;
}

function findAdjacentMatches(&$grid, $node)
{
	$candidates = [];
	foreach (range(-1,1) as $y) {
		if (!isset($grid[$y + $node->getY()])) {
			continue;
		}
		foreach (range(-1,1) as $x) {
			if ($x != 0 && $y != 0) { continue; }
			if ($x == 0 && $y == 0) { continue; }
			if (!isset($grid[$y + $node->getY()][$x + $node->getX()])) { continue; }
			$toNode = $grid[$y + $node->getY()][$x + $node->getX()];
			if ($node->getUsed() <= $toNode->getAvail()) {
				$candidates[] = $toNode;
			}
		}
	}
	return $candidates;
}

function findMatches(&$nodes, $node)
{
	$candidates = [];
	#echo "inspecting " . $node->getX() . "," . $node->getY() . "\n";
	foreach ($nodes as $y => $row) {
		foreach ($row as $x => $testNode) {
			if (
				!($x == $node->getX() && $y == $node->getY()) &&
				$node->getUsed() <= $testNode->getAvail()
			) {
				#echo "found match: " . $node->getX() . "," . $node->getY() . " -> $x,$y\n";
				$candidates[] = $testNode;
			}
		}
	}
	return $candidates;
}

function printGrid($grid) {
	foreach ($grid as $y => $row) {
		echo str_pad($y, 2) . ": ";
		foreach ($row as $x => $node) {
			echo $node . " ";
		}
		echo "\n";
	}
}



class Node
{
	private $_x;
	private $_y;
	private $_size;
	private $_used;
	private $_avail;
	private $_usePct;
	private $_goal = false;

	public function __construct($x, $y, $size, $used, $avail, $usePct)
	{
		$this->_x = $x;
		$this->_y = $y;
		$this->_size = $size;
		$this->_used = $used;
		$this->_avail = $avail;
		$this->_usePct = $usePct;
		$this->_moved = false;
	}

	public function fill($tb)
	{
		if ($this->_avail < $tb) {
			throw new Exception("Can not insert $tb TB into " . $this->_x . "," . $this->_y . " (" . $this->_avail . " TB available)");
		}
		$this->_used += $tb;
		$this->_avail -= $tb;
		$this->_moved = true;
	}

	public function moveData(Node $toNode)
	{
		$toNode->fill($this->_used);
		$this->_used = 0;
		$this->_avail = $this->_size;
		if ($this->isGoalNode()) {
			$this->_goal = false;
			$toNode->setAsGoalNode();
		}
		$this->_moved = true;

	}

	public function setAsGoalNode()
	{
		$this->_goal = true;
	}

	public function isGoalNode()
	{
		return $this->_goal;
	}

	public function getX()
	{
		return $this->_x;
	}

	public function getY()
	{
		return $this->_y;
	}

	public function getSize()
	{
		return $this->_size;
	}

	public function getUsed()
	{
		return $this->_used;
	}

	public function getAvail()
	{
		return $this->_avail;
	}

	public function getUsePct()
	{
		return $this->_usePct;
	}

	public function __toString()
	{
		$ret = "";
		if ($this->_moved) {
			$ret .= "\e[32m";
		}
		if ($this->_used == 0) {
			$ret .= "\e[31m";
		}
		if ($this->_goal) {
			$ret .= "\e[34m";
		}
		$ret .= str_pad($this->_used, 3, " ", STR_PAD_LEFT) . "/" . str_pad($this->_avail, 2, " ", STR_PAD_LEFT);
		if ($this->_goal || $this->_used == 0 || $this->_moved) {
			$ret .= "\e[0m";
		}
		return $ret;
	}
}
