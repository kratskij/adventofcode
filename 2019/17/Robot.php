<?php

require_once(__DIR__ . '/../IntCodeComputer.php');

class Robot extends IntCodeComputer {

    private $_awake = false;

    public function __construct($code) {
#        self::$_map = new Map();
        parent::__construct($code);
    }
    public function wakeup() {
        $awake = true;
        $this->_code = 2;
    }

    public function in($input)  {
        return parent::in($input, false);
    }
}
