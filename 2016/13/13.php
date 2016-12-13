<?php

$test = true;

$input = 1364;
#$input = 10;

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

#var_dump($grid);

echo
"             1         2         3         4         5         6         7         8         9         10
   01234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890
";
foreach ($grid as $y => $row) {
	echo $y . "  ";
	foreach ($row as $x => $val) {
		echo ($val) ? "." : "█";
	}
	echo "\n";
}

$routes = findRoutes($grid, 1, 1);

echo "Part 1: " . findRoutes($grid, 1, 1)[39][31] . "\n";

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

function findRoutes($grid, $fromY, $fromX) {
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
