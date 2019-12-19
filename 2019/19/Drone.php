<?php

require_once(__DIR__ . '/../IntCodeComputer.php');

class Drone extends IntCodeComputer {

    public function __construct($code) {
#        self::$_map = new Map();
        parent::__construct($code);
    }

    public function wakeup() {
        $awake = true;
        $this->_code = 2;
    }

    public function goto($x, $y)  {
        $this->in($x, false);
        $this->in($y, true);

        return $this->out();
    }
}
