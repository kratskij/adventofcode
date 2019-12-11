<?php

require_once(__DIR__ . '/../IntCodeComputer.php');

class PaintRobot extends IntCodeComputer {

    private $_grid = [];
    private static $_dirs = ["N", "E", "S", "W"];
    private $_dir;
    private $_xCoord;
    private $_yCoord;
    private $_first;

    const BLACK = 0;
    const WHITE = 1;

    const LEFT = 0;
    const RIGHT = 1;

    public function reset() {
        $this->_grid = [];
        $this->_dir = 0;
        $this->_xCoord = 0;
        $this->_yCoord = 0;
        $this->_first = true;
        parent::reset();
    }

    function getPanelPaintCount() {
        $painted = 0;
        foreach ($this->_grid as $row) {
            foreach ($row as $val) {
                $painted++;
            }
        }

        return $painted;
    }

    function getRegistrationIdentifier() {
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
                $out .= (isset($this->_grid[$y]) && isset($this->_grid[$y][$x]) && $this->_grid[$y][$x] == self::WHITE) ? "▉" : " ";
            }
            $out .= "\n";
        }

        return $out;
    }

    private function paintPanel($color) {
        $panel = $this->preparePanel();
        $this->_grid[$this->_yCoord][$this->_xCoord] = $color;
    }

    private function getPanelColor() {
        $this->preparePanel();
        return $this->_grid[$this->_yCoord][$this->_xCoord];
    }

    private function preparePanel() {
        if (!isset($this->_grid[$this->_yCoord])) {
            $this->_grid[$this->_yCoord] = [];
        }
        if (!isset($this->_grid[$this->_yCoord][$this->_xCoord])) {
            $this->_grid[$this->_yCoord][$this->_xCoord] = self::BLACK;
        }
    }

    function move($turn) {
        $this->_dir += ($turn == self::LEFT) ? -1 : 1;
        if (!isset(self::$_dirs[$this->_dir])) {
            $this->_dir = ($this->_dir < 0) ? 3 : 0;
        }

        switch (self::$_dirs[$this->_dir]) {
            case "N":
                $this->_yCoord--;
                break;
            case "S":
                $this->_yCoord++;
                break;
            case "E":
                $this->_xCoord++;
                break;
            case "W":
                $this->_xCoord--;
                break;
        }
    }

    public function paint($overrideColor) {
        while (true) {
            if ($overrideColor !== null) {
                $this->paintPanel($overrideColor);
                $overrideColor = null;
            }
            $color = $this->getPanelColor();

            try {
                $this->paintPanel($this->in($color));
                $this->move($this->in($color));
            } catch (End $e) {
                return;
            }
        }
    }
}
