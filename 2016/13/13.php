<?php

$test = true;

$input = 1364;
#$input = 10;
$animate = false;
$grid = [];
$y = 0 ;
while (true) {
	$x = 0;
	$grid[$y] = [];
	while (true) {
		$num = $x*$x + 3*$x + 2*$x*$y + $y + $y*$y;
		$bin = decbin($num + $input);
		#echo $bin."    ";
		$grid[$y][$x] = (substr_count((string)$bin, "1") % 2 == 0);
		#echo substr_count((string)$bin, "1") . "\n";
		if ($x >= 60) {
			break;
		}
		$x++;
	}
	if ($y >= 60) {
		break;
	}
	$y++;
}

$routes = findRoutes($grid, 1, 1, $animate);

echo "Part 1: " . $routes[39][31] . "\n";

$sum = array_map(
	function($row) {
		$ret = 0;
		foreach ($row as $c) {
			$ret += (is_numeric($c) && $c <= 50) ? 1 : 0;
		}
		return $ret;
	},
	$routes
);
echo "Part 2: " . array_sum($sum) . "\n";

function findRoutes($grid, $fromY, $fromX, $animate) {
	$grid[$fromY][$fromX] = 0;
	$prevHash = false;
	$gridHash = md5(serialize($grid));
	while ($prevHash != $gridHash) {
		foreach ($grid as $y => &$row) {
			foreach ($row as $x => &$val) {
				if (is_numeric($val)) {
					foreach (range(-1,1) as $i) {
						foreach (range(-1,1) as $j) {
							if ($i != 0 && $j != 0) { continue; }
							if (isset($grid[$y + $i][$x + $j])) {
								$n = &$grid[$y + $i][$x + $j];
								if ($n === true || (is_numeric($n) && $n + 1 < $n)) {
									if ($animate) {
										printGrid($grid, $y + $i, $x + $j);
									}
									$n = $val + 1;
								}
							}
						}
					}
				}
			}
		}
		$prevHash = $gridHash;
		$gridHash = md5(serialize($grid));
	}

	return $grid;
}

function printGrid($grid, $nowY = false, $nowX = false)
{
	$ret = "   ";
	foreach ($grid[0] as $i => $col) {
		$ret .= str_pad($i, 4);
	}
	$ret .= "\n";

	foreach ($grid as $y => $row) {
		$ret .= $y . "  ";
		foreach ($row as $x => $val) {
			if ($y == 39 && $x == 31) {
				$ret .= "\e[45m";
			} else if ($y == 1 && $x == 1) {
				$ret .= "\e[41m";
			} else if ($y == $nowY && $x == $nowX) {
				$ret .= "\e[42m";
			}
			/*
			$left = $row[$x - 1];
			$right = $row[$x + 1];
			$up = $grid[$y - 1][$x];
			$down = $grid[$y + 1][$x];
			$dir = "   ";
			if (is_numeric($left) && $val > $left) { $dir = " → "; }
			else if (is_numeric($right) && $val > $right) { $dir = " ← "; }
			else if (is_numeric($up) && $val > $up) { $dir = " ↓ "; }
			else if (is_numeric($down) && $val > $down) { $dir = " ↑ "; }
			*/

			$ret .= is_numeric($val) ? str_pad($val, 4) : ( $val === false ? "████" : "    ");

			if (($y == 39 && $x == 31) || ($y == 1 && $x == 1) || ($y == $nowY && $x == $nowX)) {
				$ret .= "\e[49m";
			}
		}
		$ret .= "\n";
	}
	system('clear');
	echo $ret;
	usleep(25000);
}
