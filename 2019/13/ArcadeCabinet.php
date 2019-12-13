<?php

require_once(__DIR__ . '/../IntCodeComputer.php');

class ArcadeCabinet extends IntCodeComputer {

    private $_grid = [];
    private $_xCoord;
    private $_yCoord;
    private $_tile;
    private $_outOrder = [];
    private $_first;
    private $_joyStick;

    private $_paddleX;
    private $_paddleY;
    private $_ballX;
    private $_ballY;
    private $_ballDir = 0;

    private $_blocks = [];

    private $_depth = 0;

    const JOYSTICK_NEUTRAL = 0;
    const JOYSTICK_RIGHT = 1;
    const JOYSTICK_LEFT = -1;

    public function render() {
        try {
            while(true) {
                $this->in(0);
            }
        } catch (End $e) {
            #echo "Part 1: " . strlen($p1) . "\n";
        }
    }

    public function countBlocks() {
        return count($this->_blocks);
    }

    public function cheat() {
        $this->_code[0] = 2; // gain creditz!
    }

    public function play($in) {
        #while (true) {
            $this->in($in);
            $this->_depth++;
            echo "{$this->_depth}, {$this->_yCoord}, {$this->_xCoord}, {$this->_lastOutout}, {$this->countBlocks()}\n";
        #}
    }

    public function printGrid() {
        $minY = $minX = PHP_INT_MAX;
        $maxY = $maxX = -PHP_INT_MAX;
        foreach ($this->_grid as $y => $row) {
            $minY = min($minY, $y);
            $maxY = max($maxY, $y);
            $minX = min($minX, min(array_keys($row)));
            $maxX = max($maxX, max(array_keys($row)));
        }

        $out = "\n";
        for ($y = $minY; $y <= $maxY; $y++) {
            for ($x = $minX; $x <= $maxX; $x++) {
                if ($x == $this->_ballX && $y == $this->_ballY) {
                    $out .= "o";
                } else if ($x == $this->_paddleX && $y == $this->_paddleY) {
                    $out .= "_";
                } else {
                    $out .= (isset($this->_grid[$y]) && isset($this->_grid[$y][$x])) ? $this->_grid[$y][$x] : " ";
                }
            }
            $out .= "\n";
        }

        echo $out;
    }

    public function reset() {
        $this->_grid = [];
        $this->_dir = 0;
        $this->_xCoord = 0;
        $this->_yCoord = 0;
        $this->_first = true;
        $this->_outOrder = [];
        $this->_joyStick = 0;

        parent::reset();
    }

    public function getTiles() {
        #foreach ($gen)
    }

    public function optimizePaddle() {
        if ($this->_paddleX > $this->_ballX + $this->_ballDir) {
            return -1;
        } else if ($this->_paddleX + 1 < $this->_ballX + $this->_ballDir) {
            return 1;
        }
        return 0;
    }

    public function in($input) {
        $this->_xCoord = parent::in($input);
        $this->_yCoord = parent::in($input);
        $this->_lastOutout = parent::in($input);

        if ($this->_xCoord == -1 && $this->_yCoord == 0) {
            echo "SEGMENT: {$this->_lastOutout}\n";
        } else if ($this->_lastOutout == 2) {
            $this->_grid[$this->_yCoord][$this->_xCoord] = "#";
            $this->_blocks[$this->_yCoord . "_" . $this->_xCoord] = true;
        } else if ($this->_lastOutout == 1) {
            $this->_grid[$this->_yCoord][$this->_xCoord] = "▉";
        } else {
            if (isset($this->_blocks[$this->_yCoord . "_" . $this->_xCoord])) {
                unset($this->_blocks[$this->_yCoord . "_" . $this->_xCoord]);
                unset($this->_grid[$this->_yCoord][$this->_xCoord]);
            }
            if ($this->_lastOutout == 3) {
                $this->_paddleY = $this->_yCoord;
                $this->_paddleX = $this->_xCoord;

            } else if ($this->_lastOutout == 4) {
                $this->_ballDir = $this->_xCoord - $this->_ballX;
                $this->_ballY = $this->_yCoord;
                $this->_ballX = $this->_xCoord;
            }
        }

        return $this->_lastOutout;
    }

}
