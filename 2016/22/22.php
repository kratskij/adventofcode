<?php

$test = false;

$file = ($test) ? "test.txt" : "input";
$input = explode("\n", trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file)));

$regex = '/\w+\-x(\d+)\-y(\d+)\s+(\d+)T\s+(\d+)T\s+(\d+)T\s+(\d+)%/';

$nodes = [];

foreach ($input as $row) {
	preg_match($regex, $row, $matches);
	array_shift($matches); # remove first match (which is the whole matched string)
	$matches = array_map("intval", $matches);
	#var_dump($matches);
	list($x, $y, $size, $used, $avail, $usePct) = $matches;
	$nodes[$y][$x] = new Node($x, $y, $size, $used, $avail, $usePct);
}

$pairs = [];

//Part 1
foreach ($nodes as $y1 => $row1) {
	foreach ($row1 as $x1 => $n1) {
		foreach ($nodes as $y2 => $row2) {
			foreach ($row2 as $x2 => $n2) {
				if (
					!($y1 == $y2 && $x1 == $x2) &&
					$n1->getUsed() > 0 &&
					$n1->getUsed() <= $n2->getAvail()
				) {
					$pairs[] = [$n1, $n2];
				}
			}
		}
	}
}
echo "Part 1: " . count($pairs) . "\n";


//Part 2
foreach ($nodes as $y => $row) {
	echo "$y: ";
	foreach ($row as $x => $node) {
		echo $node->getUsed()."/".$node->getAvail()." ";
	}
	echo "\n";
}


$candidates = findMatches($nodes, $nodes[0][max(array_keys($nodes[0]))]);
var_dump($candidates);

function findMatches(&$nodes, $node)
{
	$candidates = [];
	foreach (range(-1,1) as $testY) {
		if (!isset($nodes[$node->getY() + $testY])) {
			continue;
		}
		foreach (range(-1,1) as $testX) {
			if (!isset($nodes[$node->getY() + $testY][$node->getX() + $testX])) {
				continue;
			}
			$testNode = $nodes[$node->getY() + $testY][$node->getX() + $testX];

			if (
				$testX != 0 && $testY != 0 &&
				$node->getUsed() <= $testNode->getAvail()
			) {
				$candidates[] = $testNode;
			}
		}
	}
	return $candidates;
}



class Node
{
	private $_x;
	private $_y;
	private $_size;
	private $_used;
	private $_avail;
	private $_usePct;

	public function __construct($x, $y, $size, $used, $avail, $usePct)
	{
		$this->_x = $x;
		$this->_y = $y;
		$this->_size = $size;
		$this->_used = $used;
		$this->_avail = $avail;
		$this->_usePct = $usePct;
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
}
