<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";

if ($test) {
    $players = 9;
    $lastWorth = 25;
} else {
    $players = 412;
    $lastWorth = 71646;
}

echo "Part 1: " . initGame($players, $lastWorth) . "\n";
echo "Part 2: " . initGame($players, $lastWorth * 100) . "\n";

function initGame($players, $marbles) {
    $circle = new CircularLinkedList($marbles);
    $scores = [];
    for ($i = 0; $i < $players; $i++) {
        $scores[$i] = 0;
    }

    #print $circle . PHP_EOL;
    for ($i = 1; $i <= $marbles; $i++) {
        if ($i % 23 == 0) {
            $scores[$i % $players] += $i;
            $circle->prev(8);
            $scores[$i % $players] += $circle->getValue();
            $circle->unset();
        } else {
            $circle->insert($i);
        }

        #print $circle . PHP_EOL;
        $circle->next();
    }
    return max($scores). "\n";
}

class CircularLinkedList {

    private $_nodes = [];
    private $_pointer;
    private $_count = 0;

    public function __construct($count) {
        $this->_nodes[0] = new Node(0, 0, 0);
        $this->_pointer = 0;
        $this->_count++;
    }

    private function left() {
        return $this->_nodes[$this->node()->left()];
    }

    private function right() {
        return $this->_nodes[$this->node()->right()];
    }

    private function node() {
        return $this->_nodes[$this->_pointer];
    }

    public function insert($i) {
        //Current will become previous; we're inserting after current
        $this->_nodes[$this->_count] = new Node($i, $this->_pointer, $this->node()->right());
        $this->right()->setLeft($this->_count);
        $this->node()->setRight($this->_count);
        $this->_pointer = $this->_count;
        $this->_count++;
    }

    public function getValue() {
        return $this->node()->getValue();
    }

    public function next($count = 1) {
        for ($i = 0; $i < $count; $i++) {
            $this->_pointer = $this->node()->right();
        }
    }

    public function prev($count = 1) {
        for ($i = 0; $i < $count; $i++) {
            $this->_pointer = $this->node()->left();
        }
    }

    public function unset() {
        $this->right()->setLeft($this->node()->left());
        $this->left()->setRight($this->node()->right());
        $this->_pointer = $this->node()->right();
    }

    public function __toString() {
        static $times;
        if ($times === null) {
            $times = 0;
        }
        $str = $times . "  ";
        $times++;

        $startNode = $this->_nodes[0];
        $next = $this->_nodes[$startNode->right()];
        $str .= ($this->node() === $startNode) ? "({$startNode->getValue()})" : " {$startNode->getValue()} ";
        while ($next !== $startNode) {
            $str .= ($this->node() === $next) ? "({$next->getValue()})" : " {$next->getValue()} ";
            $next = $this->_nodes[$next->right()];
        }
        return $str;
    }
}

class Node {

    private $_value;
    private $_left;
    private $_right;

    public function __construct($value = 0, $left = 0, $right = 0) {
        $this->_value = $value;
        $this->_left = $left;
        $this->_right = $right;
    }

    public function getValue() {
        return $this->_value;
    }

    public function left() {
        return $this->_left;
    }

    public function right() {
        return $this->_right;
    }

    public function setLeft($prev) {
        $this->_left = $prev;
        return $this;
    }

    public function setRight($right) {
        $this->_right = $right;
        return $this;
    }
}
