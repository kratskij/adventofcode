<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";

$file = ($test) ? "test" : "input";
$input = explode("\n", trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file)));

$scanners = [];

foreach ($input as $k => $line) { #$matches?
    $p = explode(": ", $line);
    $scanners[(int)$p[0]] = range(0, (int)$p[1] - 1);
}

echo "Part 1: " . solve($scanners, true) . "\n";
echo "Part 2: " . solve($scanners, false) . "\n";

function solve($scanners, $break = false) {
	$myPos = 0;
	$picoSecond = 0;
	$severities = [];

	$startAt = 0;
	$prevPicoSecond = $picoSecond;

	while (true) {
		if (isset($scanners[$myPos])) {
			$scanner = $scanners[$myPos];
			$maxLevel = count($scanner);
			$direction = (floor($picoSecond / ($maxLevel - 1)));
			if ($direction % 2 == 0) { //moving down
				$scannerAt = $picoSecond % ($maxLevel - 1);
			} else { // moving up
				$scannerAt = ($maxLevel - 1) - ($picoSecond % ($maxLevel - 1));
			}
			if ($scannerAt == 0) {
				#echo "caught at $prevPicoSecond\n";
				if ($break) {
					$severities[] = ($maxLevel * $myPos);
				} else {
					$startAt++;
					$myPos = 0;
					$picoSecond = $prevPicoSecond + 1;
					$prevPicoSecond = $picoSecond;
					continue;
				}
			}
		}
		$picoSecond++;
		$myPos++;
		if ($myPos > max(array_keys($scanners))) {
			if ($break) {
                return array_sum($severities);
			} else {
                return $startAt;
			}
				break;
		}
	}
}



#echo $sum;
#echo $outString;
