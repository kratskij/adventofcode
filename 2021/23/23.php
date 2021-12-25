<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");
require_once(__DIR__."/../Util.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->lines();

$p1 = run($input);
$p2 = run($input, ["  #D#C#B#A#","  #D#B#A#C#"], 3);

echo "P1: $p1\nP2: $p2\n";

function run($lines, $injectLines = [], $injectAfter = null) {
    if ($injectLines) {
        $lines = array_merge(array_slice($lines, 0, $injectAfter), $injectLines, array_slice($lines, $injectAfter));
    }
    $pos = $doors = $destinations = $hallway = [];
    foreach ($lines as $y => $line) {
        foreach (str_split($line) as $x => $v) {
            if (strpos("ABCD", $v) !== false) {
                $pos["$x,$y"] = $v;
                $doors[$x][] = $y;
                $hallway[$x] = false;
            } else if ($v == ".") {
                $hallway[$x] = ($hallway[$x] ?? true) && true;
            }
        }
    }
    $hallway = array_filter($hallway);

    $owner = "A";
    $depth = 0;
    foreach ($doors as $x => $rooms) {
        foreach ($rooms as $roomY) {
            $depth = max($depth, $roomY);
            $destinations[$x.",".$roomY] = $owner;
        }
        $doors[$x] = $owner;
        $owner = chr(ord($owner) + 1);
    }

    $q = [
        [$pos, 0]
    ];

    $visited = [];
    $minScore = PHP_INT_MAX;
    while ($cur = array_pop($q)) {
        list($pos, $score) = $cur;

        $idx = implode(";", array_keys($pos)) . "#" . implode(";", $pos);
        if (isset($visited[$idx]) && $visited[$idx] <= $score) {
            continue;
        }
        $visited[$idx] = $score;

        $done = true;
        foreach ($pos as $coords => $id) {
            list($x,$y) = explode(",", $coords);
            if (isset($destinations[$coords]) && $destinations[$coords] == $id) {
                //we're home! BUT WE MIGHT BLOCK SOMEONE
                $blocking = false;
                for ($y2 = $y+1; $y2 <= $depth; $y2++) {
                    if (isset($pos["$x,$y2"]) && $pos["$x,$y2"] != $id) {
                        $blocking = true;
                        break;
                    }
                }
                if (!$blocking) {
                    //nothing to do; we're already where we're supposed to be (without blocking anyone)
                    continue;
                }
                //ouf, we're blocking someone. better get out of the way
            }
            $done = false;
            if (isset($destinations[$coords])) {
                //we're in someone else's home (or our own, but blocking someone)! let's move to the hallway
                // (or home?)

                $blocked = false;
                for ($y2 = $y-1; $y2 > 1; $y2--) {
                    if (isset($pos["$x,$y2"])) {
                        $blocked = true;
                        break;
                    }
                }
                if ($blocked) {
                    continue;
                }

                $moves = findAvailableHallwaySpots($x, $id, $hallway, $doors, $pos, $depth);

                foreach ($moves as $c2) {
                    list($x2,$y2) = explode(",", $c2);
                    if ($x2 == $x && $y2 == $y) {
                        continue;
                    }
                    $pos2 = $pos;
                    $pos2["$x2,$y2"] = $id;
                    unset($pos2["$x,$y"]);

                    $distance = ($y-1) + ($y2-1) + abs($x-$x2);
                    $newScore = $score+$distance*(pow(10, ord($id)-65));
                    if ($newScore < $minScore) {
                        $q[] = [$pos2, $newScore];
                    }
                }
            } else {
                // we're in the hallway; let's see if we can get home
                foreach ($doors as $destX => $door) {
                    if ($door == $id) {
                        break;
                    }
                }

                $blocked = false;
                for ($y2 = 2; $y2 <= $depth; $y2++) {
                    if (isset($pos["$destX,$y2"])) {
                        for ($y3 = $y2; $y3 <= $depth; $y3++) {
                            if ($pos["$destX,$y3"] != $id) {
                                $blocked = true;
                                break;
                            }
                        }
                        break;
                    }
                }
                if ($blocked) {
                    continue;
                }

                $moves = [];
                $x2 = $x+1;
                while ((isset($hallway[$x2]) || isset($doors[$x2]))) {
                    if (isset($pos["$x2,1"])) {
                        break;
                    }
                    if (isset($doors[$x2]) && $doors[$x2] == $id) {
                        $moves[] = $x2;
                    }
                    $x2++;
                }
                $x2 = $x-1;
                while ((isset($hallway[$x2]) || isset($doors[$x2]))) {
                    if (isset($pos["$x2,1"])) {
                        break;
                    }
                    if (isset($doors[$x2]) && $doors[$x2] == $id) {
                        $moves[] = $x2;
                    }
                    $x2--;
                }

                foreach ($moves as $x2) {
                    $pos2 = $pos;
                    for ($y2 = $depth; $y2 > 1; $y2--) {
                        if (!isset($pos2["$x2,$y2"])) {
                            $pos2["$x2,$y2"] = $id;
                            unset($pos2[$coords]);
                            $distance = abs($y-$y2) + abs($x-$x2);
                            $newScore = $score+$distance*(pow(10, ord($id)-65));
                            if ($newScore < $minScore) {
                                $q[] = [$pos2, $newScore];
                            }
                            break;
                        }
                    }
                }
            }
        }
        if ($done) {
            $minScore = min($minScore, $score);
            #echo "New minscore: $score\n";
            #printGrid($pos);

            $q = array_filter($q, function($el) use ($minScore) {
                return ($el[1] < $minScore);
            });
        }
    }

    return $minScore;
}

function findAvailableHallwaySpots($startX, $id, $hallway, $doors, $pos, $depth) {
    $distances = [-1,1];
    $moves = [];

    while ($distance = array_shift($distances)) {
        #echo $distance." ";
        $x2 = $startX + $distance;
        if (isset($hallway[$x2]) && !isset($pos["$x2,1"])) {
            // available spot!
            $moves[] = "$x2,1";
            $distances[] = $distance + ($distance > 0 ? 1 : -1);
            #echo "\tadding " . ($distance + ($distance > 0 ? 1 : -1)) . " from $distance\n";
        } else if (isset($doors[$x2])) {
            // someone's door? allright, we can't stop, so let's just keep moving further into the hallway ...
            $distances[] = $distance + ($distance > 0 ? 1 : -1);
            #echo "\tadding " . ($distance + ($distance > 0 ? 1 : -1)) . " (2) from $distance\n";

            // hey, wait, maybe we can move directly in, if the door is our own?
            if ($doors[$x2] == $id) {
                // yay! let's move in, as far as possible, unless there are blockers that need to get out!
                $blocking = false;
                for ($y2 = 1; $y2 < $depth; $y2++) {
                    if (!isset($pos["$x2,$y2"]) && isset($pos["$x2," . ($y2+1)])) {
                        for ($y3 = $y2+1; $y3 <= $depth; $y3++) {
                            if ($pos["$x2,$y3"] != $id) {
                                $blocking = true;
                                break;
                            }
                        }
                        break;
                    }
                }
                if (!$blocking) {
                    $moves[] = "$x2,$y2";
                }
            }
        }
    }
    #echo "\n";

    return $moves;
}

function printGrid($pos) {
    $out = "";
    for ($y = 0; $y <= 6; $y++) {
        for ($x = 0; $x <= 12; $x++) {
            $idx = "$x,$y";
            if (
                ($y == 1 && $x > 0 && $x < 12) ||
                (($y == 2 || $y == 3 || $y == 4 || $y == 5) && ($x == 3 || $x == 5 || $x == 7 || $x == 9))
            ) {
                if (isset($pos[$idx])) {
                    $out .= $pos[$idx];
                } else {
                    $out .= " ";
                }
            } else {
                $out .= "░";
            }
        }
        $out .= "\n";
    }
    echo $out;
}
