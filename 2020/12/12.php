<?php

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$directions = $ir->lines();

$p2 = new Part2();
$p1 = new Part1();

foreach ($directions as $k => $direction) {
    $cmd = $direction[0];
    $num = (int)substr($direction, 1);

    $p1->do($cmd, $num);
    $p2->do($cmd, $num);
}

echo "P1: " . $p1->distance() . "\n";
echo "P2: " . $p2->distance() . "\n";


class Part1 {
    private $x = 0;
    private $y = 0;

    private $dirs = ["E", "S", "W", "N"];
    private $dir = 0; // starting at east ($dir[0])

    public function do($cmd, $num) {
        switch ($cmd) {
            case "L":
                while ($num != 0) {
                    $this->dir = ($this->dir + 3) % 4;
                    $num -= 90;
                }
                break;
            case "R":
                while ($num != 0) {
                    $this->dir = ($this->dir + 1) % 4;
                    $num -= 90;
                }
                break;
            case "F":
                $this->move($this->dirs[$this->dir], $num);
                break;
            default:
                $this->move($cmd, $num);
                break;
        }
    }

    private function move($direction, $distance) {
        switch ($direction) {
            case "N":
                $this->y -= $distance;
                break;
            case "S":
                $this->y += $distance;
                break;
            case "E":
                $this->x += $distance;
                break;
            case "W":
                $this->x -= $distance;
                break;
        }
    }

    public function distance() {
        return (abs($this->y)+abs($this->x));
    }
}

class Part2 {
    private $x = 0;
    private $y = 0;
    private $waypointX = 10;
    private $waypointY = -1;

    public function do($cmd, $num) {
        switch ($cmd) {
            case "L":
                while ($num != 0) {
                    $tmp = $this->waypointY;
                    $this->waypointY = -$this->waypointX;
                    $this->waypointX = $tmp;
                    $num -= 90;
                }
                break;
            case "R":
                while ($num != 0) {
                    $tmp = $this->waypointY;
                    $this->waypointY = $this->waypointX;
                    $this->waypointX = -$tmp;
                    $num -= 90;
                }
                break;
            case "F":
                $this->y += $this->waypointY * $num;
                $this->x += $this->waypointX * $num;
                break;
            case "N":
                $this->waypointY -= $num;
                break;
            case "S":
                $this->waypointY += $num;
                break;
            case "E":
                $this->waypointX += $num;
                break;
            case "W":
                $this->waypointX -= $num;
                break;
        }
    }

    public function distance() {
        return (abs($this->y)+abs($this->x));
    }
}
