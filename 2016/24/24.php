<?php

$test = false;

$file = ($test) ? "../test.txt" : "input";
$input = explode("\n", trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file)));

$grid = [];

$mustVisit = [];
$distances = [0 => []];
$visitedStates = [];

$animate = true;

$mePos = false;
foreach ($input as $y => $row) {
	foreach (str_split($row) as $x => $val) {
		if (is_numeric($val)) {
			if ($val === "0") {
				$mePos = [$y, $x];
			} else {
				$mustVisit[$val] = [$y, $x];
			}
		}
		$grid[$y][$x] = is_numeric($val) ? $val : ($val !== "#");
	}
}
$queue[0] = [new World($grid, $mePos)];

foreach ($mustVisit as $v => $pos) {
	$queue[$v] = [new World($grid, $pos)];
	$distances[$v] = [];
	$visitedStates[$v] = [];
}

$c = 0;
$prevDepth = 0;
foreach ($queue as $initPos => &$worlds) {
	if ($animate) {
		$animateGrid = new World($grid, $mePos);
	}
	while ($world = array_shift($worlds)) {
		$hash = md5((string)$world);
		if (isset($visitedStates[$initPos][$hash])) {
			continue;
		}
		if ($animate && $world->steps() != $prevDepth) {
			system("clear");
			echo $animateGrid->colorFormat()."\n";
			echo "starting at $initPos, depth: {$world->steps()}";
			#usleep(20000);
		}
		$prevDepth = $world->steps();
		$visitedStates[$initPos][$hash] = true;
		$to = $world->isAtAnyOf($mustVisit);
		if ($to !== false && $to != $initPos) {
			$distances[$initPos][$to] = $world->steps();
		}
		foreach ($world->getNextLevel() as $w2) {
			$worlds[] = $w2;
			if ($animate) {
				$animateGrid->moveMe($w2->pos()[0], $w2->pos()[1]);
			}
		};
		$c++;
	}
}

$initDistances = $distances[0];
$mustVisitDistances = array_diff_key($distances, [0 => true]);
$routes = allRoutes($initDistances, $mustVisitDistances);

echo "\nPart 1: " . min($routes) . "\n";

$p2dist = [];
foreach ($routes as $route => $length) {
	$from = $route[strlen($route) - 1];
	$p2dist[$route."_0"] = $length + $distances[0][$from];
}

echo "\nPart 2: " . min($p2dist) . "\n";



function allRoutes($initDistances, $mustVisitDistances, $length = 0, $route = "")
{
	$routes = [];
	foreach ($initDistances as $pos => $dist) {
		$newMvd = $mustVisitDistances;
		if (!isset($newMvd[$pos])) {
			echo "\n\n";
			var_dump($pos, $newMvd);
			die();
		}
		$newInitDistance = $newMvd[$pos];
		unset($newMvd[$pos]);
		foreach ($newMvd as &$mvd) {
			unset($mvd[$pos]);
		}
		if (count($newMvd) == 0) {
			return [$route . "_" . $pos => $length + $dist];
		} else {
			$routes = array_merge($routes, allRoutes($newInitDistance, $newMvd, $length + $dist, $route . $pos . "_"));
		}
	}
	return $routes;
}

class World
{
	private $_mePos = false;
	private $_grid = false;
	private $_steps = 0;

	private $_visited = [];

	public function __construct($world, $mePos)
	{
		$this->_grid = $world;
		$this->_mePos = $mePos;
	}

	public function pos()
	{
		return $this->_mePos;
	}

	public function steps()
	{
		return $this->_steps;
	}

	public function isAtAnyOf(array $positions)
	{
		foreach ($positions as $key => $pos) {
			if (implode(",", $pos) == implode(",", $this->_mePos)) {
				return $key;
			}
		}
		return false;
	}

	public function getNextLevel()
	{
		$nextLevel = [];
		list($y, $x) = $this->_mePos;
		foreach (range(-1,1) as $ty) {
			foreach (range(-1,1) as $tx) {
				if (
					abs($tx) + abs($ty) != 1 ||
					!isset($this->_grid[$y + $ty]) ||
					!isset($this->_grid[$y + $ty][$x + $tx])
				) {
					continue;
				}
				if ($this->_grid[$y+$ty][$x+$tx] === false) {
					continue;
				}
				if (isset($this->_visited[$y+$ty][$x+$tx])) {
					continue;
				}

				//suboptimized for better animation
				$newGrid = clone $this;
				$newGrid->moveMe($y + $ty, $x + $tx);
				$nextLevel[] = $newGrid;
			}
		}
		return $nextLevel;
	}

	public function moveMe($y, $x)
	{
		$this->_visited[$this->_mePos[0]][$this->_mePos[1]] = true;
		$this->_mePos = [$y, $x];
	}

	public function __clone()
	{
		$this->_steps++;
	}

	public function colorFormat()
	{
		$ret = "";
		foreach ($this->_grid as $y => $row) {
			foreach ($row as $x => $val) {
				$colored = (isset($this->_visited[$y]) && isset($this->_visited[$y][$x]));
				$ret .= $colored ? "\e[42m" : "";
				if ($y == $this->_mePos[0] && $x == $this->_mePos[1]) {
					$ret .= "@";
				} else {
					$ret .= is_numeric($val) ? "\e[41m$val\e[0m" : (($val) ? " " : "#");
				}
				$ret .= $colored ? "\e[0m" : "";
			}
			$ret .= "\n";
		}

		return $ret;
	}

	public function __toString()
	{
		$ret = "";
		foreach ($this->_grid as $y => $row) {
			foreach ($row as $x => $val) {
				if ($y == $this->_mePos[0] && $x == $this->_mePos[1]) {
					$ret .= "@";
				} else {
					$ret .= is_numeric($val) ? $val : (($val) ? " " : "#");
				}
			}
			$ret .= "\n";
		}

		return $ret;
	}
}
