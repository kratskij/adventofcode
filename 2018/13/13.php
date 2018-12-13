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

foreach ($input as $line) {
    $grid[] = str_split($line);
}

$dirMap = array_flip(["<", "^", ">", "v"]);

$carts = [];
foreach ($grid as $y => $row) {
    foreach ($row as $x => $val) {
        if (isset($dirMap[$val])) {
            $carts[] = [
                "x" => $x,
                "y" => $y,
                "alive" => true,
                "deadSince" => false,
                "dir" => $dirMap[$val],
                "isectDir" => ISECT_LEFT
            ];
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

$remainingCarts = $carts;
while (true) {
    usort($carts, function($a, $b) {
        if ($a["y"] == $b["y"]) {
            return $a["x"] - $b["x"];
        }
        return $a["y"] - $b["y"];
    });
    foreach ($carts as $idx => &$c) {
        if (!$c["alive"]) {
            continue;
        }
        switch ($c["dir"]) {
            case CART_LEFT:
                $nextPosValue = $grid[$c["y"]][--$c["x"]];
                break;
            case CART_RIGHT:
                $nextPosValue = $grid[$c["y"]][++$c["x"]];
                break;
            case CART_UP:
                $nextPosValue = $grid[--$c["y"]][$c["x"]];
                break;
            case CART_DOWN:
                $nextPosValue = $grid[++$c["y"]][$c["x"]];
                break;
        }
        switch ($nextPosValue) {
            case "/":
                if ($c["dir"] == CART_LEFT) {
                    $c["dir"] = CART_DOWN;
                } else if ($c["dir"] == CART_RIGHT) {
                    $c["dir"] = CART_UP;
                } else if ($c["dir"] == CART_UP) {
                    $c["dir"] = CART_RIGHT;
                } else if ($c["dir"] == CART_DOWN) {
                    $c["dir"] = CART_LEFT;
                }
                break;
            case "\\":
                if ($c["dir"] == CART_LEFT) {
                    $c["dir"] = CART_UP;
                } else if($c["dir"] == CART_RIGHT) {
                    $c["dir"] = CART_DOWN;
                } else if ($c["dir"] == CART_UP) {
                    $c["dir"] = CART_LEFT;
                } else if ($c["dir"] == CART_DOWN) {
                    $c["dir"] = CART_RIGHT;
                }
                break;
            case "+":
                if ($c["isectDir"] == ISECT_LEFT) {
                    $c["dir"]--;
                    if ($c["dir"] == -1) {
                        $c["dir"] = CART_DOWN;
                    }
                } else if ($c["isectDir"] == ISECT_RIGHT) {
                    $c["dir"] = ($c["dir"] + 1) % 4;
                }
                $c["isectDir"] = ($c["isectDir"] + 1) % 3;
        }

        for ($idx2 = 0; $idx2 < count($carts); $idx2++) {
            if ($idx2 != $idx && $carts[$idx2]["alive"] && $carts[$idx2]["x"] == $c["x"] && $carts[$idx2]["y"] == $c["y"]) {
                $c["alive"] = false;
                $carts[$idx2]["alive"] = false;
                $carts[$idx2]["deadSince"] = 0;

                static $p1;
                if ($p1 === null) {
                    $p1 = true;
                    echo "Part 1: " . $c["x"] . "," . $c["y"] . "\n";
                }
            }
        }
    }

    $alive = array_filter($carts, function($cart) { return $cart["alive"]; });
    if (count($alive) == 1) {
        $cart = array_shift($alive);
        echo "Part 2: {$cart['x']},{$cart['y']}\n";
        die();
    }

    if ($animate) {
        foreach ($carts as $idx => $cart) {
            if (!$carts[$idx]["alive"] && $carts[$idx]["deadSince"] < 50) {
                $carts[$idx]["deadSince"]++;
            }
        }
        animate($grid, $carts, 1000);
    }

}

function animate($grid, $carts, $interval) {
    static $frameNum;
    if ($frameNum === null) {
        $frameNum = 0;
    }
    $frameNum++;

    $out = "";
    $dirMap = ["◀", "▲", "▶", "▼"];
    $colorize = [];
    foreach ($carts as $c) {
        if ($c["deadSince"] < 50) {
            for ($i = 0; $i < $c["deadSince"]; $i++) {
                $colorize[$c["y"]+$i][$c["x"]+($c["deadSince"]-$i)] = true;
                $colorize[$c["y"]+$i][$c["x"]-($c["deadSince"]-$i)] = true;
                $colorize[$c["y"]-$i][$c["x"]+($c["deadSince"]-$i)] = true;
                $colorize[$c["y"]-$i][$c["x"]-($c["deadSince"]-$i)] = true;
            }
        }
    }
    foreach ($grid as $y => $row) {
        foreach ($row as $x => $val) {
            $cartFound = false;
            foreach ($carts as $idx3 => $cart) {
                if ($cart["x"] == $x && $cart["y"] == $y) {
                    $out .= ($cart["alive"] ? "\033[1;32m" : "\033[1;31m") . $dirMap[$cart["dir"]] . "\033[0m";
                    $cartFound = true;
                    continue 2;
                }
            }
            if (isset($colorize[$y][$x])) {
                $out .= "\033[1;31m" . $val . "\033[0m";
            } else {
                $out .= $val;
            }
        }
        $out .= "\n";
    }

    $out .= "\n$frameNum\n\n";
    system("clear");
    echo $out;
    usleep($interval);
}
