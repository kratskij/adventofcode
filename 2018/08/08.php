<?php

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file);
$input = $ir->explode(" ");

$tree = makeTree($input);

echo "Part 1:" . $tree->findSum()."\n";
echo "Part 2:" . $tree->findSum2()."\n";

function makeTree(&$input) {
    $childCount = array_shift($input);
    $metaEntryCount = array_shift($input);

    $children = [];
    for ($i = 0; $i <$childCount; $i++) {
        $children[] = parseInput($input);
    }

    for ($i = 0; $i < $metaEntryCount; $i++) {
        $metaEntries[] = array_shift($input);
    }
    $tree = new Tree($metaEntries);

    foreach ($children as $c) {
        $tree->add($c);
    }

    return $tree;
}


class Tree
{
	private $_level;
	private $_node;
	private $_children = [];
	private $_parent;
	private static $_previousStates = [];

    public static $_maxLevel = 0;

	public function __construct($node, $level = 0, Tree $parent = null)
	{
        self::$_maxLevel = max(self::$_maxLevel, $level);
        $this->_node = $node;
		$this->_level = $level;
		$this->_parent = $parent;
	}

	public function add(Tree $tree)
	{
		$this->_children[] = $tree;
    }


    public function findSum() {
        $sum = array_sum($this->_node);
        foreach ($this->_children as $c) {
            $sum += $c->findSum();
        }

        return $sum;
    }
    public function findSum2() {
        if (!empty($this->_children)) {
            $sum = 0;
            foreach ($this->_node as $c) {
                if (isset($this->_children[$c-1])) {
                    $sum += $this->_children[$c-1]->findSum2();
                }
            }
        } else {
            $sum = array_sum($this->_node);
        }

        return $sum;
    }

	public function node()
	{
		return $this->_node;
	}

	public function getChildren()
	{
		return $this->_children;
	}

	public function parent()
	{
		return $this->_parent;
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
		$lines = implode(",", $this->_node);
		foreach (explode("\n", $lines) as $line) {
			$ret .= $prefix . $line . "\n";
		}

		foreach ($this->_children as $child) {
			$ret .= $child . "\n";
		}

		return $ret;
	}
}
