<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file);
$input = explode("\n", $ir->raw());
$animate = false;

$grid = [];

define("CART_LEFT", 0);
define("CART_UP", 1);
define("CART_RIGHT", 2);
define("CART_DOWN", 3);

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
            if ($val == "<" || $val == ">") {
                $grid[$y][$x] = "-";
            } elseif ($val == "^" || $val == "v") {
                $grid[$y][$x] = "|";
            } else {
                throw new Exception("NO0");
            }

        }
    }
}

$seconds = 0;
$remainingCarts = $carts;
while (true) {
    usort($carts, function($a, $b) {
        if ($a["y"] == $b["y"]) {
            return $a["x"] - $b["x"];
        }
        return $a["y"] - $b["y"];
    });
    for ($idx = 0; $idx < count($carts); $idx++) {
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
                    break;
                case CART_DOWN:
                    $atNextPos = $grid[++$carts[$idx]["y"]][$carts[$idx]["x"]];
                    break;
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
                case "\\":
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

                    static $p1;
                    if ($p1 === null) {
                        $p1 = true;
                        echo "Part 1: " . $carts[$idx]["x"] . "," . $carts[$idx]["y"] . "\n";
                    }
                }
            }
        }

        if (count($remainingCarts) == 1) {
            $cart = reset($remainingCarts);
            echo "Part 2: {$cart['x']},{$cart['y']}\n";
            die();
        }
        $remainingCarts = array_filter(
            $carts,
            function($cart) { return $cart["in"]; }
        );
    }

    if ($animate) {
        $out = "";
        foreach ($grid as $y => $row) {
            foreach ($row as $x => $val) {
                $cartFound = false;
                $cartCopy = $carts;
                foreach ($cartCopy as $idx3 => $cart) {
                    if ($cart["x"] == $x && $cart["y"] == $y) {
                        if (!$cart["in"]) {
                            $out .= "\033[1;31mX\033[0m";
                        } else {
                            $out .= "\033[1;37m".array_flip($dirMap)[$cart["dir"]]."\033[0m";
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
        system("clear");
        echo $out;
        usleep(1000000);
    }

}
echo $val;
