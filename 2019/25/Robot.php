<?php

require_once(__DIR__ . '/../IntCodeComputer.php');

class Robot extends IntCodeComputer {

    private $_debug;

    private $_doors = [];
    private $_items = [];
    private $_roomName = false;

    public function __construct($code, $debug = false) {
        parent::__construct($code);
        $this->_debug = $debug;
    }

    public function reset() {
        parent::reset();
        $this->_commands = [];
    }

    public function south() { return $this->move("south"); }
    public function north() { return $this->move("north"); }
    public function west() { return $this->move("west"); }
    public function east() { return $this->move("east"); }

    public function move($dir) {
        $this->_commands[] = "$dir";
        return $this->act();
    }

    public function take($that) {
        $this->_commands[] = "take $that";
        return $this->act();
    }

    public function drop($album) {
        $this->_commands[] = "drop $album";
        return $this->act();
    }

    public function inv() {
        $this->_commands[] = "inv";
        return $this->act();
    }

    private function act() {
        while ($command = array_shift($this->_commands)) {
            if ($this->_debug) {
                echo "INPUT: $command\n";
            }
            foreach (str_split($command) as $chr) {
                $this->in(ord($chr), false);
            }
            $this->in(10);
        }
        return $this->read();
    }

    public function find($what, $visited = []) {
        if (in_array($what, $this->_items)) {
            if ($this->_debug) {
                echo "Found $what in {$this->_roomName}\n";
            }

            $this->take($what);
            return $this;
        } else {
            foreach ($this->_doors as $door) {
                echo "Trying $door\n";
                $that = clone $this;
                $that->move($door);
                if (in_array($that->roomName(), $visited)) {
                    continue;
                }
                $visited[] = $that->roomName();

                $ret = $that->find($what, $visited);
                if ($ret) {
                    return $ret;
                }
            }
        }
        echo "Found Could not find $what\n";
        return false;
    }

    private function roomName() {
        return $this->_roomName;
    }

    public function read() {
        $line = $state = false;
        $found = [
            "doors" => [],
            "items" => [],
        ];
        while ($line != "Command?") { //remove newline
            $out = false;
            $line = "";
            while ($out != 10) {
                $prevOut = $out;
                $out = $this->in(0);
                if ($this->_debug) {
                    echo chr($out);
                }
                $line .= chr($out);
            }
            $line = substr($line, 0, -1);

            if ($state && substr($line, 0, 2) == "- ") {
                $found[$state][] = substr($line, 2);
            }
            if ($line == "Doors here lead:") {
                $state = "doors";
            } else if ($line == "Items here:") {
                $state = "items";
            } else if (substr($line, 0, 2) == "==") {
                $this->_roomName = substr($line, 3, -3);
            }
        }

        $this->_doors = $found["doors"];
        $this->_items = $found["items"];

        return $this;
    }

    public function cli() {
        while (true) {
            $this->act();
            $this->_commands[] = readline();
            $this->_commands = array_filter($this->_commands);
        }
    }
}
