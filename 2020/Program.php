<?php

class Program {
    public $_instructions;
    public $_pointer = 0;
    private $_exitCode = self::NOT_FINISHED;

    public static $debug = false;

    const NOT_FINISHED = "NOT_FINISHED";
    const CODE_NOT_FOUND = "CODE_NOT_FOUND";
    const CODE_LOOP = "CODE_LOOP";
    const UNKNOWN_COMMAND = "UNKNOWN_COMMAND";

    protected function jmp($value) {
        $this->_pointer += $value - 1;
    }
    protected function nop($value) {
        //skip, for now
    }

    public function __construct(array $instructions) {
        foreach ($instructions as $ins) {
            $parts = explode(" ", $ins);
            $this->_instructions[] = [
                "cmd" => array_shift($parts),
                "arguments" => $parts,
                "fulltext" => $ins,
            ];
        }
    }

    public function reset() {
        $this->_pointer = 0;
        $this->_visited = [];
        $this->_exitCode = self::NOT_FINISHED;
    }

    public function run() {
        $this->reset();
        if (self::$debug) {
            echo "Starting program...\n";
        }

        while ($this->step());

        return $this->_exitCode;
    }

    private function step() {
        if (!isset($this->_instructions[$this->_pointer])) {
            if (self::$debug) {
                echo "Program pointer (line {$this->_pointer}) not found!\n";
            }
            $this->_exitCode = self::CODE_NOT_FOUND;
            return false;
        }
        if (isset($this->_visited[$this->_pointer])) {
            if (self::$debug) {
                echo "Already visited line {$this->_pointer} ({$this->_instructions[$this->_pointer]['fulltext']})\n";
            }
            $this->_exitCode = self::CODE_LOOP;
            return false;
        }
        if (self::$debug) {
            #echo "DEBUG: At {$this->_instructions[$this->_pointer]['fulltext']}\n";
        }

        $this->_visited[$this->_pointer] = true;

        if (!method_exists($this, $this->getCurrentCommand())) {
            echo "UNKNOWN COMMAND: {$this->_instructions[$this->_pointer]['fulltext']} (at line {$this->_pointer})\n";
            $this->_exitCode = self::UNKNOWN_COMMAND;
            return false;
        }
        call_user_func_array(
            [$this, $this->getCurrentCommand()],
            $this->getCurrentArguments()
        );
        $this->increase();

        return true;
    }

    public function increase() {
        $this->_pointer++;
    }

    public function getCurrentCommand() {
        return $this->_instructions[$this->_pointer]["cmd"] ?? false;
    }

    public function getCurrentArguments() {
        return $this->_instructions[$this->_pointer]["arguments"] ?? [];
    }

    public function exitCode() {
        return $this->_exitCode;
    }
}
