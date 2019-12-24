<?php

require_once(__DIR__ . '/../IntCodeComputer.php');

class SpringDroid extends IntCodeComputer {

    private $_debug;

    public function __construct($code, $debug = false) {
        parent::__construct($code);
        $this->_debug = $debug;
    }


    public function reset() {
        parent::reset();
        $this->_commands = [];
    }

    public function not($reg1, $reg2) {
        $this->_commands[] = "NOT $reg1 $reg2";
        return $this;
    }

    public function or($reg1, $reg2) {
        $this->_commands[] = "OR $reg1 $reg2";
        return $this;
    }

    public function and($reg1, $reg2) {
        $this->_commands[] = "AND $reg1 $reg2";
        return $this;
    }

    public function walk() {
        $this->_commands[] = "WALK";
        return $this->go();
    }

    public function run() {
        $this->_commands[] = "RUN";
        return $this->go();
    }

    private function go() {
        while (true) {
            $out = false;
            while ($out != 10) {
                $out = $this->in(0, false);
                if ($out > 255) {
                    return $out;
                }
                if ($this->_debug) {
                    echo chr($out);
                }
            }

            while ($command = array_shift($this->_commands)) {
                if ($this->_debug) {
                    echo "INPUT: $command\n";
                }
                foreach (str_split($command) as $chr) {
                    $this->in(ord($chr), false);
                }
                $this->in(10, false);
            }
        }
    }
}
