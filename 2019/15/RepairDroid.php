<?php

require_once(__DIR__ . '/../IntCodeComputer.php');

class Map {
    private $_grid = [];
    private $_oxygenPosition = null;

    const WALL = 0;
    const EMPTY = 1;
    const OXYGEN = 2;

    public function set($y, $x, $value) {
        if ($value == self::OXYGEN) {
            $this->_oxygenPosition = [$y, $x];
        }
        $this->_grid[$y][$x] = $value;
    }

    public function getOxygenPosition() {
        return $this->_oxygenPosition;
    }

    public function get($y, $x) {
        return isset($this->_grid[$y]) && isset($this->_grid[$y][$x]) ? $this->_grid[$y][$x] : null;
    }

    public function __toString() {
        $minY = min(array_keys($this->_grid));
        $maxY = max(array_keys($this->_grid));
        $minX = PHP_INT_MAX;
        $maxX = -PHP_INT_MAX;
        for ($y = $minY; $y <= $maxY; $y++) {
            $minX = min($minX, min(array_keys($this->_grid[$y])));
            $maxX = max($maxX, max(array_keys($this->_grid[$y])));
        }

        $str = "";
        for ($y = $minY; $y <= $maxY; $y++) {
            for ($x = $minX; $x <= $maxX; $x++) {
                if (isset($this->_grid[$y][$x])) {
                    if ($this->_grid[$y][$x] === self::EMPTY) {
                        $str .= "░";
                    } else if ($this->_grid[$y][$x] === self::WALL) {
                        $str .= "█";
                    }
                } else {
                    $str .= " ";
                }
            }
            $str .= "\n";
        }

        return $str;
    }
}

class RepairDroid extends IntCodeComputer {
    const NORTH = [1, -1,  0];
    const SOUTH = [2,  1,  0];
    const WEST =  [3,  0, -1];
    const EAST =  [4,  0,  1];

    private static $_map;
    private $_x;
    private $_y;

    public function __construct($code) {
        self::$_map = new Map();
        parent::__construct($code);
    }

    public function getMap() {
        return self::$_map;
    }

    private function getX() {
        return $this->_x;
    }

    private function getY() {
        return $this->_y;
    }

    public function setX($x) {
        $this->_x = $x;
    }

    public function setY($y) {
        $this->_y = $y;
    }

    public function shortestPath($to) {
        list($toY, $toX) = $to;
        $queue = [ [$this, 0] ];

        $visited = [];

        $steps = 0;
        while ($queue) {
            list($repairDroid, $steps) = array_shift($queue);
            foreach ([self::NORTH, self::EAST, self::SOUTH, self::WEST] as $dir) {
                $droid = clone $repairDroid;
                $newY = $droid->getY() + $dir[1];
                $newX = $droid->getX() + $dir[2];

                if ($newX == $toX && $newY == $toY) {
                    return $steps + 1;
                }
                if (!isset($visited[$newY][$newX]) && self::$_map->get($newY, $newX) === Map::EMPTY) {
                    $droid->setX($newX);
                    $droid->setY($newY);
                    $visited[$newY][$newX] = true;
                    $queue[] = [$droid, $steps + 1];
                }
            }
        }
    }

    public function spreadTime($from) {
        list($fromY, $fromX) = $from;
        $queue = [ [$this, 0] ];
        $visited = [];

        while ($queue) {
            list($repairDroid, $steps) = array_shift($queue);
            foreach ([self::NORTH, self::EAST, self::SOUTH, self::WEST] as $dir) {
                $droid = clone $repairDroid;
                $newY = $droid->getY() + $dir[1];
                $newX = $droid->getX() + $dir[2];

                if (!isset($visited[$newY][$newX]) && self::$_map->get($newY, $newX) === Map::EMPTY) {
                    $droid->setX($newX);
                    $droid->setY($newY);
                    $visited[$newY][$newX] = true;
                    $queue[] = [$droid, $steps + 1];
                }
            }
        }

        return $steps;
    }

    function mapArea($animate = false) {
        self::$_map->set(0, 0, Map::EMPTY);
        $this->setY(0);
        $this->setX(0);
        $queue = [$this];

        while ($queue) {
            $repairDroid = array_shift($queue);
            foreach ([self::NORTH, self::EAST, self::SOUTH, self::WEST] as $dir) {
                $newY = $repairDroid->getY() + $dir[1];
                $newX = $repairDroid->getX() + $dir[2];

                if (self::$_map->get($newY, $newX) !== null) {
                    continue;
                }

                $droid = clone $repairDroid;
                $status = $droid->in($dir[0]);

                self::$_map->set($newY, $newX, $status);
                switch ($status) {
                    case Map::WALL:
                        break;
                    case Map::EMPTY:
                        $droid->setY($newY);
                        $droid->setX($newX);
                        $queue[] = $droid;
                        break;
                    case Map::OXYGEN;
                        break;
                    default:
                        die("WAT");
                }
                if ($animate) {
                    usleep(10000);
                    system("clear");
                    echo $droid->getMap();
                }
            }
        }
    }

}
