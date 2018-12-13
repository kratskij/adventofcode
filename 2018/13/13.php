<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file);
$input = explode("\n", $ir->raw());

$grid = [];

define("CART_LEFT", 0);
define("CART_UP", 1);
define("CART_RIGHT", 2);
define("CART_DOWN", 3);

define("TRACK_UPDOWN", "|/\+");
define("TRACK_LEFTRIGHT", "-/\+");

define ("ISECT_LEFT", 0);
define ("ISECT_STRAIGHT", 1);
define ("ISECT_RIGHT", 2);



foreach ($input as $k => $line) {
    $grid[$k] = str_split($line);
}

$dirMap = [
    "<" => CART_LEFT,
    ">" => CART_RIGHT,
    "^" => CART_UP,
    "v" => CART_DOWN,
];
$carts = [];
foreach ($grid as $y => $row) {
    foreach ($row as $x => $val) {
        if (isset($dirMap[$val])) {
            $carts[] = ["x" => $x, "y" => $y, "in" => true, "dir" => $dirMap[$val], "nextIsect" => ISECT_LEFT];

            if (
                strpos(TRACK_UPDOWN, $grid[$y-1][$x]) !== false &&
                strpos(TRACK_UPDOWN, $grid[$y+1][$x]) !== false
            ) {
                $grid[$y][$x] = "|";
            } else if (
                strpos(TRACK_LEFTRIGHT, $grid[$y][$x-1]) !== false &&
                strpos(TRACK_LEFTRIGHT, $grid[$y][$x+1]) !== false
            ) {
                $grid[$y][$x] = "-";
            } else {
                throw new Exception("NO0");
            }

        }
    }
}

$seconds = 0;
$dieAtNext = false;
while (true) {
    echo "NEW SECOND!" . ++$seconds."\n";
    for ($idx = 0; $idx < count($carts); $idx++) {
        #echo "IDX " . $idx."\n";
        if ($carts[$idx]["in"]) {
            switch ($carts[$idx]["dir"]) {
                case CART_LEFT:
                    $atNextPos = $grid[$carts[$idx]["y"]][--$carts[$idx]["x"]];
                    break;
                case CART_RIGHT:
                    $atNextPos = $grid[$carts[$idx]["y"]][++$carts[$idx]["x"]];
                    break;
                case CART_UP:
                    $atNextPos = $grid[--$carts[$idx]["y"]][$carts[$idx]["x"]];
                    if (!isset($grid[$carts[$idx]["y"]][$carts[$idx]["x"]])) {
                        var_Dump($carts[$idx], $seconds, $idx);
                        die();
                    }
                    break;
                case CART_DOWN:
                    $atNextPos = $grid[++$carts[$idx]["y"]][$carts[$idx]["x"]];
                    break;
            }
            if ($idx == 3) {
                #echo $c["y"].",".$c["x"].",",$grid[$c["y"]][$c["x"]]."\n";
            }
            if (!isset($grid[$carts[$idx]["y"]][$carts[$idx]["x"]])) {
                #var_dump("OUT OF GRID", $c, $idx); die();
            }
            switch ($atNextPos) {
                case "/":
                    if ($carts[$idx]["dir"] == CART_LEFT) {
                        $carts[$idx]["dir"] = CART_DOWN;
                    } else if ($carts[$idx]["dir"] == CART_RIGHT) {
                        $carts[$idx]["dir"] = CART_UP;
                    } else if ($carts[$idx]["dir"] == CART_UP) {
                        $carts[$idx]["dir"] = CART_RIGHT;
                    } else if ($carts[$idx]["dir"] == CART_DOWN) {
                        $carts[$idx]["dir"] = CART_LEFT;
                    }
                    break;
                case '\\':
                    if ($carts[$idx]["dir"] == CART_LEFT) {
                        $carts[$idx]["dir"] = CART_UP;
                    } else if($carts[$idx]["dir"] == CART_RIGHT) {
                        $carts[$idx]["dir"] = CART_DOWN;
                    } else if ($carts[$idx]["dir"] == CART_UP) {
                        $carts[$idx]["dir"] = CART_LEFT;
                    } else if ($carts[$idx]["dir"] == CART_DOWN) {
                        $carts[$idx]["dir"] = CART_RIGHT;
                    }
                    break;
                case "+":
                    if ($carts[$idx]["nextIsect"] == ISECT_LEFT) {
                        $carts[$idx]["dir"]--;
                        if ($carts[$idx]["dir"] == -1) {
                            $carts[$idx]["dir"] = CART_DOWN;
                        }
                    } else if ($carts[$idx]["nextIsect"] == ISECT_RIGHT) {
                        $carts[$idx]["dir"] = ($carts[$idx]["dir"] + 1) % 4;
                    }
                    $carts[$idx]["nextIsect"] = ($carts[$idx]["nextIsect"] + 1) % 3;
            }

            for ($idx2 = 0; $idx2 < count($carts); $idx2++) {
                if ($idx2 != $idx && $carts[$idx2]["in"] && $carts[$idx2]["x"] == $carts[$idx]["x"] && $carts[$idx2]["y"] == $carts[$idx]["y"]) {
                    $carts[$idx]["in"] = false;
                    $carts[$idx2]["in"] = false;

                    echo "CRASH AT " . $carts[$idx]["x"] . "," . $carts[$idx]["y"] . " after $seconds seconds!\n";
                    #break 3;
                }
            }


            if ($dieAtNext !== false) {
                var_Dump(array_filter($carts, function($c) { return $c["in"]; }));
                die();
            }
            echo "LEFT: " . count(array_filter(array_column($carts, "in")))."\n";
            if (count(array_filter(array_column($carts, "in"))) == 1) {
                #var_Dump(array_filter($carts, function($c) { return $c["in"]; }));
                #die();
                $dieAtNext = true;
            }
        } else {
            #echo "outcart?";
        }
    }

    for ($idx3 = 0; $idx3 < count($carts); $idx3++) {
        echo $idx3;
        if (!$carts[$idx3]["in"]) {
            continue;
        }
        for ($idx2 = 0; $idx2 < count($carts); $idx2++) {
            if ($idx2 != $idx3 && $carts[$idx2]["in"] && $carts[$idx2]["x"] == $carts[$idx3]["x"] && $carts[$idx2]["y"] == $carts[$idx3]["y"]) {
                $carts[$idx3]["in"] = false;
                $carts[$idx2]["in"] = false;

                echo "IT HAPPENED AT " . $carts[$idx3]["x"] . "," . $carts[$idx3]["y"] . " after $seconds seconds!\n";
                die();
                #break 3;
            }
        }
    }
    /*
    $out = "";
    foreach ($grid as $y => $row) {
        foreach ($row as $x => $val) {
            $cartFound = false;
            $cartCopy = $carts;
            foreach ($cartCopy as $idx3 => $cart) {
                if ($cart["x"] == $x && $cart["y"] == $y) {
                    if (!$cart["in"]) {
                        $out .= "X";
                    } else {
                        $out .= array_flip($dirMap)[$cart["dir"]];
                    }
                    $cartFound = true;
                    break;
                }
            }
            if (!$cartFound) {
                $out .= $val;
            }
        }
        $out .= "\n";
    }
    $out .= "\n";
    system(clear);
    echo $out;
    #usleep(500);
    */

#var_Dump($grid);
}
echo $val;

#37,46

#p2: 95,54
#p2: 96,54
