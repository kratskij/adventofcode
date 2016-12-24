<?php

$test = false;

$file = ($test) ? "../test.txt" : "input";
$input = explode("\n", trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file)));

$grid = [];

$mustVisit = [];
$distances = [0 => []];
$visitedStates = [];

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
		$grid[$y][$x] = ($val !== "#");
	}
}
$queue[0] = [new World($grid, $mePos)];
foreach ($mustVisit as $v => $pos) {
	$queue[$v] = [new World($grid, $pos)];
	$distances[$v] = [];
	$visitedStates[$v] = [];
}

foreach ($queue as $initPos => &$worlds) {
	echo "starting at $initPos\n";
	while ($world = array_shift($worlds)) {
		#echo "-------------------------\n";
		#echo $world."\n";
		$hash = md5((string)$world);
		if (isset($visitedStates[$initPos][$hash])) {
			#echo "already been here " . implode(",", $world->pos()) . "\n";
			continue;
		}
		#echo "first time at " . implode(",", $world->pos()) . "\n";
		$visitedStates[$initPos][$hash] = true;
		$to = $world->isAtAnyOf($mustVisit);
		if ($to !== false && $to != $initPos) {
			#echo "hopp: $to: {$world->steps()}\n";
			$distances[$initPos][$to] = $world->steps();
		}
		foreach ($world->getNextLevel() as $w2) {
			$worlds[] = $w2;
		};
		#echo "world count (at $initPos): " . count($worlds)."\n";
	}
}

$initDistances = $distances[0];
$mustVisitDistances = array_diff_key($distances, [0 => true]);
var_dump($initDistances, $mustVisitDistances);
$routes = allRoutes($initDistances, $mustVisitDistances);
var_dump($routes);
echo "\nPart 1: " . min($routes) . "\n";

$p2dist = [];
foreach ($routes as $route => $length) {
	$from = $route[strlen($route) - 1];
	$p2dist[$route."_0"] = $length + $distances[0][$from];
}
var_dump($p2dist);
echo "\nPart 2: " . min($p2dist) . "\n";



function allRoutes($initDistances, $mustVisitDistances, $length = 0, $route = "")
{
	echo "here we go ($route = $length)!\n";
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
			echo "found a match! (" . $route . $pos . "_)" . ($length + $dist) . "\n";
			return [$route . "_" . $pos => $length + $dist];
		} else {
			$routes = array_merge($routes, allRoutes($newInitDistance, $newMvd, $length + $dist, $route . $pos . "_"));
		}
	}
	return $routes;
}

#var_dump($distances);

class World
{
	private $_mePos = false;
	private $_grid = false;
	private $_steps = 0;

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

				$newGrid = clone $this;
				#try {
					$newGrid->moveMe($y + $ty, $x + $tx);
				#} catch (Exception $e) {
				#	continue;
				#}
				$nextLevel[] = $newGrid;
			}
		}
		return $nextLevel;
	}

	private function moveMe($y, $x)
	{
		$this->_mePos = [$y, $x];
	}

	public function __clone()
	{
		$this->_steps++;
	}

	public function __toString()
	{
		$ret = "";
		foreach ($this->_grid as $y => $row) {
			foreach ($row as $x => $val) {
				if ($y == $this->_mePos[0] && $x == $this->_mePos[1]) {
					$ret .= "@";
				} else {
					$ret .= ($val) ? " " : "#";
				}
			}
			$ret .= "\n";
		}
		$ret .= "@" . implode(",", $this->_mePos) . "\n";

		return $ret;
	}
}
