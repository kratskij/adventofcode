<?php

$test = false;
$animate = false;

$file = ($test) ? "../test.txt" : "input";
$input = explode("\n", trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file)));

$regex = '/\w+\-x(\d+)\-y(\d+)\s+(\d+)T\s+(\d+)T\s+(\d+)T\s+(\d+)%/';

$grid = new Grid();

$maxX = 0;
foreach ($input as $row) {
	preg_match($regex, $row, $matches);
	array_shift($matches); # remove first match (which is the whole matched string)
	list($x, $y, $size, $used, $avail, $usePct) = array_map("intval", $matches);
	$grid->add($x, $y, $size, $used);
	$maxX = max($x, $maxX);
}

$count = 0;
foreach ($grid->getNodes() as $y1 => $row1) {
	foreach ($row1 as $x1 => $n1) {
		foreach ($grid->getNodes() as $y2 => $row2) {
			foreach ($row2 as $x2 => $n2) {
				if ($n1->getUsed() == 0) {
					continue;
				}
				if ($x1 == $x2 && $y1 == $y2) {
					continue;
				}
				if ($n1->getUsed() <= $n2->getAvail()) {
					$count++;
				}
			}
		}
	}
}

echo "Part 1: $count\n";
$grid->setGoalNode($maxX, 0);

$prevDepth = -1;

$shortestPositions = [];  //a grid of best grids

const SEARCH_FOR_GOAL_DATA = 0;
const MOVE_DATA_TO_ORIGO = 1;
$status = SEARCH_FOR_GOAL_DATA;

$gridQueue = [$grid];
while ($grid = array_shift($gridQueue)) {
	if (
		$animate &&
		(
			$grid->getDepth() != $prevDepth ||
			($status == MOVE_DATA_TO_ORIGO && $grid->getGoalNodeX() == 0 && $grid->getGoalNodeY() == 0)
		)
	) {
		system("clear");
		echo "\e[42m \e[0m Altered node\n";
		echo "\e[41m \e[0m Empty node\n";
		echo "\e[44m \e[0m Contains precious data\n";
		echo "\ndepth: " . $grid->getDepth() . "\n";
		echo $grid . "\n";
		usleep(100000);
	}
	$prevDepth = $grid->getDepth();

	if ($status == SEARCH_FOR_GOAL_DATA && $grid->getPreciousNode()->hasMoved()) {
		// We're finally able to move the data!
		// Reset queue and start searching for origo!
		$status = MOVE_DATA_TO_ORIGO;
		$gridQueue = [];
		$shortestPositions = [];
	} else if ($status == MOVE_DATA_TO_ORIGO && $grid->getGoalNodeX() == 0 && $grid->getGoalNodeY() == 0) {
		echo "Part 2: " . $grid->getDepth() . "\n";
		die();
	}

	$newGrids = $grid->getNextLevelGrids();
	foreach ($newGrids as $ng) {
		if ($status == SEARCH_FOR_GOAL_DATA) {
			$shortest = [$ng->getEmptyX(), $ng->getEmptyY()];
		} else if ($status == MOVE_DATA_TO_ORIGO) {
			if ($grid->getGoalNodeY() != $ng->getGoalNodeY() || $grid->getGoalNodeX() != $ng->getGoalNodeX()) {
				// We moved the goal node! Soon it's time to celebrate!
				$shortest = [$ng->getGoalNodeX(), $ng->getGoalNodeY()];
			} else {
				// Aww, we only moved other stuff around. might need that, though!
				if (abs($ng->getGoalNodeX() - $ng->getEmptyX()) > 1 || abs($ng->getGoalNodeY() - $ng->getEmptyY()) > 1) {
					// Too far apart!
					continue;
				}
				$shortest = [$ng->getGoalNodeX() . "_" . $ng->getEmptyX(), $ng->getGoalNodeY() . "_" . $ng->getEmptyY()];
			}

		}
		if (
			isset($shortestPositions[$shortest[1]][$shortest[0]]) &&
			$shortestPositions[$shortest[1]][$shortest[0]]->getDepth() <= $ng->getDepth()
		) {
			// we've already found this path using fewer steps. Kill this path
			continue;
		}
		#echo "adding to queue!";
		$shortestPositions[$shortest[1]][$shortest[0]] = $ng;
		$gridQueue[] = $ng;
	}
}


function findMatches(&$nodes, $node, $nodeY, $nodeX)
{
	$candidates = [];
	#echo "inspecting " . $node->getX() . "," . $node->getY() . "\n";
	foreach ($nodes as $y => $row) {
		foreach ($row as $x => $testNode) {
			if (
				!($x == $nodeX && $y == $nodeY) &&
				$node->getUsed() <= $testNode->getAvail()
			) {
				#echo "found match: " . $node->getX() . "," . $node->getY() . " -> $x,$y\n";
				$candidates[] = $testNode;
			}
		}
	}
	return $candidates;
}

class Grid
{
	private $_nodes = [];
	private $_depth = 0;

	private $_emptyPos = [];
	private $_preciousPos = [];

	public function add($x, $y, $size, $used)
	{
		if (!isset($this->_nodes[$y])) {
			$this->_nodes[$y] = [];
		}
		$node = new Node($size, $used);
		$this->_nodes[$y][$x] = $node;
		if ($node->getUsed() == 0) {
			$this->_emptyPos = [$x, $y];
		}
	}

	public function setGoalNode($x, $y)
	{
		$this->_nodes[$y][$x]->hasPreciousData(true);
		$this->_preciousPos = [$x, $y];
	}

	public function moveData($fromX, $fromY, $toX, $toY)
	{
		$fromNode = $this->_nodes[$fromY][$fromX];
		$toNode = $this->_nodes[$toY][$toX];

		$toNode->fill($fromNode->getUsed());
		$fromNode->setUsed(0);
		$this->_emptyPos = [$fromX, $fromY];
		if ($fromNode->hasPreciousData()) {
			$fromNode->hasPreciousData(false);
			$toNode->hasPreciousData(true);
			$this->_preciousPos = [$toX, $toY];
		}
	}

	public function getPreciousNode()
	{
		return $this->_nodes[$this->_preciousPos[1]][$this->_preciousPos[0]];
	}

	public function getGoalNodeX()
	{
		return $this->_preciousPos[0];
	}

	public function getGoalNodeY()
	{
		return $this->_preciousPos[1];
	}

	public function getNextLevelGrids()
	{
		$nextLevel = [];
		list($x, $y) = $this->_emptyPos;
		foreach (range(-1,1) as $ty) {
			foreach (range(-1,1) as $tx) {
				if (
					abs($tx) + abs($ty) != 1 ||
					!isset($this->_nodes[$y + $ty]) ||
					!isset($this->_nodes[$y + $ty][$x + $tx])
				) {
					continue;
				}
				$newGrid = clone $this;
				try {
					$newGrid->moveData($x + $tx, $y + $ty, $x, $y);
				} catch (Exception $e) {
					continue;
				}
				$nextLevel[] = $newGrid;
			}
		}
		return $nextLevel;
	}

	public function getEmptyX()
	{
		return $this->_emptyPos[0];
	}

	public function getEmptyY()
	{
		return $this->_emptyPos[1];
	}

	public function getDepth()
	{
		return $this->_depth;
	}

	public function getNodes()
	{
		return $this->_nodes;
	}

	public function __toString()
	{
		$ret = "";
		foreach ($this->_nodes as $y => $row) {
			$ret .= str_pad($y, 2) . ": ";
			foreach ($row as $x => $node) {
				$ret .= $node;
			}
			$ret .= "\n";
		}
		return $ret;
	}

	public function __clone()
	{
		$this->_depth++;
		foreach ($this->_nodes as $y => $row) {
			foreach ($row as $x => $node) {
				$this->_nodes[$y][$x] = clone $node;
			}
		}
	}
}

class Node
{
	private $_size;
	private $_used;
	private $_goal = false;
	private $_moved = false;

	public function __construct($size, $used)
	{
		$this->_size = $size;
		$this->_used = $used;
	}

	public function fill($tb)
	{
		if ($this->getAvail() < $tb) {
			throw new Exception("Can not insert $tb TB (" . $this->getAvail() . " TB available)");
		}
		$this->_used += $tb;
		$this->_moved = true;
	}

	public function hasPreciousData($setTo = null)
	{
		if (is_null($setTo)) {
			return $this->_goal;
		}
		$this->_goal = $setTo;
	}

	public function hasMoved()
	{
		return $this->_moved;
	}

	public function getUsed()
	{
		return $this->_used;
	}

	public function setUsed($used)
	{
		$this->_used = $used;
	}

	public function getAvail()
	{
		return $this->_size - $this->_used;
	}

	public function __toString()
	{
		$ret = "";
		if ($this->_moved) {
			$ret .= "\e[42m";
		}
		if ($this->_used == 0) {
			$ret .= "\e[41m";
		}
		if ($this->_goal) {
			$ret .= "\e[44m";
		}
		$ret .= #str_pad($this->_used, 3, " ", STR_PAD_LEFT) . "/" .
			str_pad($this->getAvail(), 2, " ", STR_PAD_LEFT);
		$ret .= " ";
		if ($this->_goal || $this->_used == 0 || $this->_moved) {
			$ret .= "\e[0m";
		}
		return $ret;
	}
}
