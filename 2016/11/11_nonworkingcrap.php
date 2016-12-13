<?php

$test = false;

const GENERATOR = "G";
const CHIP = "M";

const EMPTY_FLOOR = 1;
const ONLY_GENERATORS = 2;
const ONLY_CHIPS = 3;
const GENERATORS_AND_ONE_CHIP = 4;
const CHIPS_AND_ONE_GENERATOR = 5;
const CHIPS_AND_GENERATORS = 6;
const WTF = 7;
const ONE_GENERATOR = 8;
const ONE_CHIP = 9;

$floors = [
	["SG"=>"SG","SM"=>"SM","PG"=>"PG","PM"=>"PM"],
	["TG"=>"TG","RG"=>"RG","CG"=>"CG","RM"=>"RM","CM"=>"CM"],
	["TM"=>"TM"],
	[]
];

if ($test) {
	$floors = [
		["HM" => "HM", "LM" => "LM"],
		["HG" => "HG"],
		["LG" => "LG"],
		[]
	];
}
$pos = $steps = 0;

while (count($floors[3]) != array_sum(array_map("count", $floors))) {

	//print building
	echo "\n";
	for ($a = 3; $a >= 0; $a--) {
		echo ($a == $pos) ? "* " : "  ";
		echo "F" . $a . ": " . implode("  ", $floors[$a])."\n";
	}

	switch(analyzeFloor($floors[$pos])) {
		case EMPTY_FLOOR:
		 	echo "                  EMPTY_FLOOR\n";
			break;
		case ONLY_GENERATORS:
		 	echo "                  ONLY_GENERATORS\n";
			break;
		case ONLY_CHIPS:
		 	echo "                  ONLY_CHIPS\n";
			break;
		case GENERATORS_AND_ONE_CHIP:
		 	echo "                  GENERATORS_AND_ONE_CHIP\n";
			break;
		case CHIPS_AND_ONE_GENERATOR:
		 	echo "                  CHIPS_AND_ONE_GENERATOR\n";
			break;
		case CHIPS_AND_GENERATORS:
		 	echo "                  CHIPS_AND_GENERATORS\n";
			break;
		case WTF:
		 	echo "                  WTF\n";
			break;
		case ONE_GENERATOR:
		 	echo "                  ONE_GENERATOR\n";
			break;
		case ONE_CHIP:
		 	echo "                  ONE_CHIP\n";
			break;
	}
	//end of debug output
	if ($pos == max(array_keys($floors))) {
		echo "TOPPETASJEN\n";
		list($chips, $gens) = splitIntoChipsAndGenerators($floors[$pos]);
		foreach ($chips as $atom => $chip) {
			if (!isset($gens[$atom])) { //this one is suitable for moving down
				move($floors, $pos, $pos-1, [$chip], $steps);
				continue 2;
			}
		}
	}
	switch (analyzeFloor($floors[$pos])) {
		case WTF:
			die("WTF!?");
		case ONLY_CHIPS:
			if (emptyBelow($pos, $floors)) {
				list($chips,) = splitIntoChipsAndGenerators($floors[$pos]);
				list($chipsAbove, $gens) = splitIntoChipsAndGenerators($floors[$pos+1]);
				switch (analyzeFloor($floors[$pos+1])) {
					case EMPTY_FLOOR:
					case ONLY_CHIPS:
						//we can move two chips at once!
						$items = array_slice($floors[$pos], 0, 2);
						move($floors, $pos, $pos+1, $items, $steps);
						break 2;
					case ONLY_GENERATORS:
						$items = [];
						foreach ($chips as $chip) {
							if (in_array($floors[$pos+1][$chip[0].GENERATOR], $gens)) {
								$items[] = $chip;
							}
						}
						if ($items > 2) {
							array_slice($items, 0, 2);
						}
						move($floors, $pos, $pos+1, $items, $steps);
						break 2;

					default:
						//we can only move one. find a suitable one
						foreach ($gens as $atom => $item) {
							if (isset($floors[$pos][$atom.CHIP])) {
								$items = [];
								$items[] = $floors[$pos][$atom.CHIP];
								move($floors, $pos, $pos+1, $items, $steps);
								break 3;
							}
						}
						throw new Exception("Couldn't move chip");

				}

			} else {
				move($floors, $pos, $pos - 1, [reset($chips)], $steps);
				break;
			}

		case ONLY_GENERATORS:
			$items = array_slice($floors[$pos], 0, 2);
			move($floors, $pos, $pos+1, $items, $steps);
			break;
		case EMPTY_FLOOR:
			throw new Exception ("You're stuck on an empty floor; There's no way to get out of here!");
		case CHIPS_AND_GENERATORS:
			list($chips, $gens) = splitIntoChipsAndGenerators($floors[$pos]);
			$items = [];
			if (count($chips) == 1 && count($gens) > 1) {
				if (!emptyBelow($pos, $floors)) {
					//we must bring the chip to another floor (since the elevator requires a chip or generator)
					$chip = reset($chips);
					$items[] = $chip;
					if (in_array($chip[0].GENERATOR, $floors[$pos]) && !emptyBelow($pos, $floors)) {
						for ($i = $pos - 1; $i >= 0; $i--) {
							switch (analyzeFloor($floors[$i])) {
								case ONLY_CHIPS:
								case ONE_CHIP:
									move($floors, $pos, $i, $items, $steps);
									break 3;
							}
						}
					}
				}
				//find a pair and move it up
				$chip = reset($chips);
				if (!in_array($chip[0].GENERATOR, $floors[$pos])) {
					throw new Exception("What to do?");
				}
				$items = [$chip, $floors[$pos][$chip[0].GENERATOR]];
				move($floors, $pos, $pos+1, $items, $steps);
				break;
#				throw new Exception("hm");
			}
			if (isset($floors[$pos+1]) && count($floors[$pos+1]) == 0) {
				//empty above; move two chips there
				$items = array_slice($chips, 0, 2);
				move($floors, $pos, $pos+1, $items, $steps);
				break;
			}
			switch(analyzeFloor($floors[$pos + 1])) {
				case ONLY_CHIPS:
				case ONE_CHIP:
					//let's find some generators and move them to their chips
					list($chips,) = splitIntoChipsAndGenerators($floors[$pos + 1]);
					list(, $gens) = splitIntoChipsAndGenerators($floors[$pos]);
					$items = [];
					foreach ($gens as $atom => $gen) {
						if (isset($chips[$atom]) || count($items) == count($chips)) {
							$items[$atom.GENERATOR] = $floors[$pos][$atom.GENERATOR];
						}
						if (count($items) == 2) {
							break;
						}
					}
					$items = array_slice($items, 0, 2);
					move($floors, $pos, $pos+1, $items, $steps);
					break 2;
			}

			foreach ($chips as $atom => $chip) {
				//find pairs that can be moved up
				if (isset($gens[$atom])) {
					$gen = $atom.GENERATOR;
					$items[] = $chip;
					$items[] = $gen;
					move($floors, $pos, $pos+1, $items, $steps);
					break 2;
				}
			}
	}
}

echo "DONE. $steps STEPS\n";

function emptyBelow($pos, $floors) {
	for ($i = $pos - 1; $i >= 0; $i--) {
		if (!empty($floors[$i])) {
			return false;
		}
	}
	return true;
}

function splitIntoChipsAndGenerators($floor)
{
	$chips = $gens = [];
	if (!is_array($floor)) {
		var_Dump($floor);
		debug_print_backtrace();
		die();
	}
	foreach ($floor as $item) {
		$atom = $item[0];
		$type = $item[1];
		if ($type == "M") {
			$chips[$atom] = $item;
		}
		if ($type == "G") {
			$gens[$atom] = $item;
		}
	}
	return [ $chips, $gens ];
}

function analyzeFloor($floor)
{
	list($chips, $gens) = splitIntoChipsAndGenerators($floor);
	if (!$chips && !$gens) {
		return EMPTY_FLOOR;
	}
	if (!$chips) {
		if (count($gens) == 1) {
			return ONE_GENERATOR;
		}
		return ONLY_GENERATORS;
	}
	if (!$gens) {
		if (count($chips) == 1) {
			return ONE_CHIP;
		}
		return ONLY_CHIPS;
	}

	//we have both
	if (count($gens) > 1 && count($chips) == 1 && isset($gens[reset($chips)])) {
		throw new Exception("You died: " . implode($floor));
	}
	if (count($gens) > 1 && count($chips) == 1 && isset($chips[reset($gens)])) {
		throw new Exception("You died: " . implode($floor));
	}

	return CHIPS_AND_GENERATORS;
}

function move(&$floors, &$pos, $toPos, $items, &$steps) {
	if (count($items) == 0) {
		#debug_print_backtrace();
		throw new Exception("Cannot move elevator without any items");
	}
	if (!isset($floors[$toPos])) {
		#debug_print_backtrace();
		throw new Exception("Floor $toPos does not exist!");
	}
	echo "Moving " . implode(" and ", $items) . " from $pos to $toPos\n";
	$from = &$floors[$pos];
	$to = &$floors[$toPos];
	foreach ($items as $item) {
		if (!isset($from[$item])) {
			throw new Exception("Cannot remove $item from [" . implode(", ", array_keys($from)) . "]");
		}
		if (isset($to[$item])) {
			throw new Exception("Cannot insert $item to [" . implode(", ", array_keys($to)) . "]");
		}
		$to[$item] = $item;
		unset($from[$item]);
	}
	$steps += abs($toPos - $pos);
	$pos = $toPos;
}
