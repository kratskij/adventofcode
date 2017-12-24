<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";

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

foreach ($input as $k => $line) {
    $values[] = array_map("intval", explode("/", $line));
}

$bridges = [];
$tree = new Tree(0);
trav($tree, $values);

echo "Part 1: " . $tree->findMax(false) . "\n";
echo "Part 2: " . $tree->findMax(true) . "\n";

function trav(&$tree, $values, $must = 0) {
    foreach ($values as $k => $val) {
        if (in_array($must, $val)) {
            $nt = $tree->add(array_sum($val));
            $rest = array_diff($val, [$must]);
            if (count($rest) == 1) {
                $rest = reset($rest);
            } else {
                $rest = $must;
            }
            trav($nt, array_diff_key($values, [$k => true]), $rest);
        }
    }
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

	public function add($node)
	{
        $c = new Tree($node, $this->_level + 1, $this);

		$this->_children[] = $c;
        return $c;
    }

    public function findMax($onlyMaxLevel)
    {
        $values = [0];
        foreach ($this->_children as $child) {
            $values[] = $child->findMax($onlyMaxLevel);
        }

        if ($onlyMaxLevel) {
            if (!$this->_children && $this->_level == self::$_maxLevel) {
                return $this->_node;
            }

            if (max($values)) {
                return $this->_node + max($values);
            }

            return 0;
        }

        return $this->_node + max($values);
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
		$lines = implode("/", $this->_node);
		foreach (explode("\n", $lines) as $line) {
			$ret .= $prefix . $line . "\n";
		}

		foreach ($this->_children as $child) {
			$ret .= $child . "\n";
		}

		return $ret;
	}
}
