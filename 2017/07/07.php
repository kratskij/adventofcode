<?php

ini_set('memory_limit','2048M');

$test = false;

$file = ($test) ? "test" : "input";
$input = explode("\n", trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file)));
#$input = str_split($input[0]);
#$input = preg_match ("//" , $input, &$matches)
#var_dump($input);

$regex = "//";
$values = [];
$outString = "";
$sum = 0;
$c = 0;

$tree = new Tree();

foreach ($input as $k => $line) { #$matches?

    $p = explode(" -> ", $line);
	#preg_match($regex, $row, $matches);
	#list(, $turn, $length) = $matches; #var_dump($matches);

	$nodeparts = explode(" ", $p[0]);
	$subs = (isset($p[1])) ? explode(", ", $p[1]) : [];

	$weight = str_replace("(", "", $nodeparts[1]);
	$weight = str_replace(")", "", $weight);
	$weight = (int)$weight;

	if ($oldTree = $tree->find($nodeparts[0])) {
		$oldTree->weight($weight);
		foreach ($subs as $s) {
			$oldSubTree = $tree->find($s);
			if (!$oldSubTree) {
				$oldTree->insert($s);
			} else {
				$tree->remove($oldSubTree);
				$oldTree->insert($oldSubTree);
			}
		}
	} else {
		#var_dump($nodeparts);
		$newTree = new Tree($nodeparts[0], false, $weight);
		foreach ($subs as $s) {
			$oldTree = $tree->find($s);
			if (!$oldTree) {
				$newTree->insert($s);
			} else {
				$tree->remove($oldTree);
				$newTree->insert($oldTree);
			}
		}
		$tree->insert($newTree);
	}
    ##echo $line;
}
echo($tree);

class Tree {
	private $_node = false;
	private $_parent = false;
	private $_children = [];
	private $_weight = 0;

	function __construct($node = false, $parent = false, $weight = 0) {
		#echo "HEI" . get_class($node)."\n\n";
		$this->_node = $node;
		$this->_parent = $parent;
		$this->_weight = $weight;
	}

	function parent($parent = false)
	{
		if ($parent) {
			$this->_parent = $parent;
			return;
		}
		return $this->_parent;
	}


	function weight($weight = false)
	{
		if ($weight === false) {
			$sum = $this->_weight;

			foreach ($this->_children as $child) {
				$sum += $child->weight();
			}
			return $sum;
		}
		$this->_weight = $weight;
	}
	function ownWeight()
	{
		return $this->_weight;
	}

	function node()
	{
		return $this->_node;
	}
	function children($children = false)
	{
		if (is_array($children)) {
			$this->_children = array_merge($this->_children, $children);
			return;
		}
		return $this->_children;
	}

	function insert($val, $weight = false) {
		if (is_object($val) && get_class($val) == get_class($this)) {
			$nt = new Tree($val->node(), $this, $val->ownWeight());
			$nt->children($val->children());
		} else {
			$nt = new Tree($val, $this, $weight);
		}
		$this->_children[] = $nt;
	}
	function remove($node) {
		foreach ($this->_children as $k => $c) {
			if ($node == $c) {
				unset($this->_children[$k]);
				return;
			}
		} 
		foreach ($this->_children as $k => $c) {
			$c->remove($node);
		} 
	}

	function move($child, $nodeId) {
		$this->_remove($child);
		$this->find($nodeId)->insert($child->node(), $child->ownWeight());
	}

	function find($val) {
		##echo "looking for $val! " . get_class($this->_node);
		if ($val == $this->_node) {
			#echo "found!\n";
			return $this;
		}
		#echo "not found (" . $this->_node . ")\n";

		foreach ($this->_children as $child) {
			if ($ret = $child->find($val)) {
				return $ret;
			}
		}
	}

	function balanced()
	{
		$childWeight = 0;
		$weights = [];
		foreach ($this->_children as $child) {
			$childWeight += $child->weight();
			if (!isset($weights[$child->weight()])) {
				$weights[$child->weight()] = 0;
			}
			$weights[$child->weight()]++;
		}
		$expectedWeight = ($weights) ? array_search(max($weights), $weights) : 0;

		if (count($weights) > 1) {
			//unbalanced!
			$rest = array_diff_key($weights, [$expectedWeight => true]);
			var_dump($weights);
			foreach ($rest as $w => $x) {
				foreach ($this->_children as $child) {
					if ($child->weight() == $w) {
						echo $child->node() . "IS ROUGE!\n";
						echo $child->weight() . "/" . $child->ownWeight() . "\n";
						echo "IS " . $child->ownWeight() . " SHOULDABEEN " . (($child->ownWeight() - ($child->weight() - $expectedWeight))) . "\n";
			
					}
				}
			}
		}

		$prevWeight = false;
		$allChildrenAreBalanced = true;
				
		foreach ($this->_children as $child) {
			if ($child->balanced() && $child->weight() != $expectedWeight) {
				}
			if ($prevWeight !== false && $child->weight() != $prevWeight) {
#				echo $child->parent()->node() . " IS UNBALANCED" . $prevWeight . "/" . $child->weight() . "/" . $this->_weight . "\n";
#				echo $child->ownWeight() - ($child->weight() - $childWeight) . "\n";
				return false;
			}
			$prevWeight = $child->weight();
		}
		return true;
	}

	function __toString() {
		return (int)$this->balanced();# . ("(" . $this->ownWeight() . "/" . $this->weight() . ")\n  ") . $this->_node . "(" . implode(",", $this->_children) . ")";
	}
}