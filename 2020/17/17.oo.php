<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");


$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$grid = $ir->grid(["#" => true, "." => false]);
/*
$p1 = new Cycle(3);
var_dump($p1);
foreach ($grid as $y => $row) {
    foreach ($row as $x => $val) {
        if ($val) {
            $p1->setValue(true, 0, $y, $x);
        }
    }
}
var_dump($p1);

#$p1->cycle();
*/



$structure = [];
$l = count($grid);
$structure[0][0] = $grid;

$structure = step($structure, 4);
#var_dump($structure);
$sum = 0;
foreach ($structure as $z) {
    foreach ($z as $w) {
        foreach ($w as $y) {
            foreach ($y as $x) {
                if ($x) {
                    $sum++;
                }
            }
        }
    }
}

echo $sum."\n";

function traverse($structure, $dimensions, $min = null, $max = null) {
    if ($min === null) {
        $mins = $maxes = [];
        $nexts = [$structure];
        for ($dim = $dimensions; $dim > 0; $dim--) {
            $ns = [];
            foreach ($nexts as $next) {
                $ns = array_merge($ns, $next);
                $mins[$dim] = min($mins[$dim] ?? PHP_INT_MAX, min(array_keys($next)));
                $maxes[$dim] = max($maxes[$dim] ?? 0, max(array_keys($next)));
            }
            $nexts = $ns;
        }
    }

    if ($dimensions > 1) {
        foreach ($structure as $structure);
    }
}


function step($structure, $dimensions) {
    static $dirs;
    if ($dirs === null) {
        $dirs = [
            3 => [
                [-1,0,1], [-1,0,-1], [-1,-1,-1], [-1,-1,0], [-1,-1,1], [-1,1,-1], [-1,1,0], [-1,1,1], [-1,0,0],
                [ 0,0,1], [ 0,0,-1], [ 0,-1,-1], [ 0,-1,0], [ 0,-1,1], [ 0,1,-1], [ 0,1,0], [ 0,1,1],
                [ 1,0,1], [ 1,0,-1], [ 1,-1,-1], [ 1,-1,0], [ 1,-1,1], [ 1,1,-1], [ 1,1,0], [ 1,1,1], [1,0,0],
            ],
            4 => [
                [-1,0,1,-1], [-1,0,-1,-1], [-1,-1,-1,-1], [-1,-1,0,-1], [-1,-1,1,-1], [-1,1,-1,-1], [-1,1,0,-1], [-1,1,1,-1], [-1,0,0,-1],
                [ 0,0,1,-1], [ 0,0,-1,-1], [ 0,-1,-1,-1], [ 0,-1,0,-1], [ 0,-1,1,-1], [ 0,1,-1,-1], [ 0,1,0,-1], [ 0,1,1,-1], [0,0,0,-1],
                [ 1,0,1,-1], [ 1,0,-1,-1], [ 1,-1,-1,-1], [ 1,-1,0,-1], [ 1,-1,1,-1], [ 1,1,-1,-1], [ 1,1,0,-1], [ 1,1,1,-1], [1,0,0,-1],

                [-1,0,1,0], [-1,0,-1,0], [-1,-1,-1,0], [-1,-1,0,0], [-1,-1,1,0], [-1,1,-1,0], [-1,1,0,0], [-1,1,1,0], [-1,0,0,0],
                [ 0,0,1,0], [ 0,0,-1,0], [ 0,-1,-1,0], [ 0,-1,0,0], [ 0,-1,1,0], [ 0,1,-1,0], [ 0,1,0,0], [ 0,1,1,0],
                [ 1,0,1,0], [ 1,0,-1,0], [ 1,-1,-1,0], [ 1,-1,0,0], [ 1,-1,1,0], [ 1,1,-1,0], [ 1,1,0,0], [ 1,1,1,0], [1,0,0,0],

                [-1,0,1,1], [-1,0,-1,1], [-1,-1,-1,1], [-1,-1,0,1], [-1,-1,1,1], [-1,1,-1,1], [-1,1,0,1], [-1,1,1,1], [-1,0,0,1],
                [ 0,0,1,1], [ 0,0,-1,1], [ 0,-1,-1,1], [ 0,-1,0,1], [ 0,-1,1,1], [ 0,1,-1,1], [ 0,1,0,1], [ 0,1,1,1], [0,0,0,1],
                [ 1,0,1,1], [ 1,0,-1,1], [ 1,-1,-1,1], [ 1,-1,0,1], [ 1,-1,1,1], [ 1,1,-1,1], [ 1,1,0,1], [ 1,1,1,1], [1,0,0,1],
            ],
        ];
    }


    $nwmin = 0;
    $nwmax = count($structure) - 1;
    $nzmin = 0;
    $nzmax = count($structure[0]) - 1;
    $nymin = 0;
    $nymax = count($structure[0][0]) - 1;
    $nxmin = 0;
    $nxmax = count($structure[0][0][0]) - 1;

    for ($i = 0; $i < 6; $i++) {
        $copy = $structure;

        $wmin = $nwmin;
        $wmax = $nwmax;

        $zmin = $nzmin;
        $zmax = $nzmax;
        $ymin = $nymin;
        $ymax = $nymax;
        $xmin = $nxmin;
        $xmax = $nxmax;
        #echo "$i: $wmin,$wmax,$zmin,$zmax,$ymin,$ymax,$xmin,$xmax\n";

        for ($w = $wmin-1; $w <= $wmax+1; $w++) {
            for ($z = $zmin-1; $z <= $zmax+1; $z++) {
                #echo "|$z,$w|\n";
                for ($y = $ymin-1; $y <= $ymax+1; $y++) {
                    for ($x = $xmin-1; $x <= $xmax+1; $x++) {
                        $c = 0;
                        foreach ($dirs[$dimensions] as $dir) {
                            list($zd,$xd,$yd,$wd) = $dir;

                            if (isset($structure[$z+$zd][$w+$wd][$y+$yd][$x+$xd]) && $structure[$z+$zd][$w+$wd][$y+$yd][$x+$xd]) {
                                $c++;
                            }
                        }

                        if (isset($structure[$z][$w][$y][$x]) && $structure[$z][$w][$y][$x] && $c != 2 && $c != 3) {
                            $copy[$z][$w][$y][$x] = false;
                        } else if ((!isset($structure[$z][$w][$y][$x]) || !$structure[$z][$w][$y][$x]) && $c == 3) {
                            $nwmin = min($nwmin, $w);
                            $nwmax = max($nwmax, $w);

                            $nzmin = min($nzmin, $z);
                            $nzmax = max($nzmax, $z);
                            $nymin = min($nymin, $y);
                            $nymax = max($nymax, $y);
                            $nxmin = min($nxmin, $x);
                            $nxmax = max($nxmax, $x);
                            $copy[$z][$w][$y][$x] = true;
                        }
                        #echo (isset($copy[$z][$w][$y][$x]) && $copy[$z][$w][$y][$x]) ? "#" : ".";
                    }
                    #echo "\n";
                }
            }
        }

        $structure = $copy;
    }

    return $structure;
}


class HyperSpace {
    protected $_dimensions;
    protected $_subDimensions = [];

    public function __construct($dimensions) {
        $this->_dimensions = $dimensions;
    }

    public function setValue() {
        $args = func_get_args();
        $value = array_shift($args);

        if ($this->_dimensions != count($args)) {
            var_dump($this->_dimensions);
            throw new Exception("ERROR: " . $this->_dimensions . " != " . (count($args) + 1));
        }

        $subDimension = array_shift($args);
        if (empty($args)) {
            $this->_subDimensions[$subDimension] = $value;
        } else {
            if (!isset($this->_subDimensions[$subDimension])) {
                echo ".";
                $this->_subDimensions[$subDimension] = new HyperSpace($this->_dimensions - 1);
            }
            $this->_subDimensions[$subDimension]->setValue($value, ...$args);
        }
    }

    public function getValue() {
        $args = func_get_args();

        if ($this->_dimensions != count($args)) {
            throw new Exception("ERROR: " . $this->_dimensions . " != " (count($args) + 1));
        }

        $subDimension = array_shift($args);
        if (!isset($this->_subDimensions[$subDimension])) {
            return false;
        }
        if (empty($args)) {
            return $this->_subDimensions[$subDimension];
        }
        return $this->_subDimensions[$subDimension]->getValue($args);
    }

    public function min() {
        return min(array_keys($this->_subDimensions));
    }

    public function max() {
        return max(array_keys($this->_subDimensions));
    }

    public function getNeighbours() {
        $min = $this->min();
        $max = $this->max();

        for ($subDimension = $min; $subDimension <= $max; $subDimension++) {
            for ($dir = -1; $dir <= 1; $dir++) {
                if (isset($this->_subDimensions[$subDimension + $dir])) {
                    $this->_subDimensions[$subDimension]->getNeighbours();
                }
            }
        }

    }
}



class Cycle extends HyperSpace{
    public function __construct() {
        parent::__construct(...func_get_args());
    }

    public function cycle($positions == null) {
        if ($positions === null) {
            $positions = [];
        }
    }

    public function countNeighbours() {
        $args = func_get_args();

        $sum = 0;
        foreach ($this->getDirections() as $dir) {
            $pos = [];
            for ($i = 0; $i < count($args); $i++) {
                $pos[$i] = $args[$i] + $dir[$i];
            }
            if ($this->get(...$pos)) {
                $sum++;
            }
        }

        return $sum;
    }

    private function getDirections() {
        static $dirs;
        if ($dirs === null) {
            $dirs = [];
        }
        if (!isset($dirs[$this->_dimensions])) {
            $dirs = [
                1 => [ [-1], [1] ]
            ];
            $i = 2;
            while ($i <= $this->_dimensions) {
                $allLowerDirs = $dirs[$i-1];
                $allLowerDirs[] = array_fill(0, $i-1, 0);
                foreach ($allLowerDirs as $dir) {

                    foreach ([-1, 0, 1] as $newDir) {
                        $nDir = $dir;
                        $nDir[] = $newDir;
                        if (array_filter($nDir)) {
                            $dirs[$i][] = $nDir;
                        }
                    }
                }
                $i++;
            }
        }
        return $dirs[$this->_dimensions];
    }
}
