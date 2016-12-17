<?php

$input = "mmsxrhfx";

#test
#$input = "hijkl";
#$input = "ihgpwlah";


$posX = $posY = 0;

$dir = "";
$queue = [[$input, 0, 0]];
$validPaths = [];

while ($queue) {
	$next = array_shift($queue);
	list($nextDir, $posX, $posY) = $next;

	if ($posX == 3 && $posY == 3) {
		$validPaths[] = substr($nextDir, strlen($input));
		continue;
	}
	$hash = substr(md5($nextDir), 0, 4);
	foreach (str_split($hash) as $key => $door) {
		if (in_array($door, ["b", "c", "d", "e", "f"])) {
			//door is open
		 	if ($key == 0 && $posY > 0) {
				$queue[] = [$nextDir."U", $posX, $posY-1];
			} else if ($key == 1 && $posY < 3) {
				$queue[] = [$nextDir."D", $posX, $posY+1];
			} else if ($key == 2 && $posX > 0) {
				$queue[] = [$nextDir."L", $posX-1, $posY];
			} else if ($key == 3 && $posX < 3) {
				$queue[] = [$nextDir."R", $posX+1, $posY];
			}
		}
	}
}
echo "Part 1: " . array_shift($validPaths) . "\n";
echo "Part 2: " . strlen(array_pop($validPaths)) . "\n";
