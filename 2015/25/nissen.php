<?php
$colz = 3029;
$rowz = 2947;

#$colz = 3;
#$rowz = 2;

$next = 20151125;
$rows = 0;
while (true) {
	$rows++;
	$row = $rows;
	for ($col = 1; $col < $rows; $col++) {
		$row = $rows - $col;
		echo $row." " . $col . " " . $next . "\n";
		if ($row == $rowz && $col == $colz) {
			echo $next . "\n";
			die();
		}
		$next = ($next * 252533) % 33554393;
		
	}
}