<?php

class Program {
    private $_instructions;
    private $_pointer = 0;
    private $_accumulator = 0;
    private $_exitCode = self::NOT_FINISHED;

    public static $debug = false;

    const NOT_FINISHED = "NOT_FINISHED";
    const CODE_NOT_FOUND = "CODE_NOT_FOUND";
    const CODE_LOOP = "CODE_LOOP";
    const UNKNOWN_COMMAND = "UNKNOWN_COMMAND";

    private function jmp($value) {
        $this->_pointer += $value - 1;
    }
    private function acc($value) {
        $this->_accumulator += $value;
    }
    private function nop($value) {
        //skip, for now
    }
    private function die($value1, $value2) {
        echo "DIEING $value1, $value2";
        die();
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

    private function reset() {
        $this->_pointer = 0;
        $this->_accumulator = 0;
        $this->_visited = [];
    }

    public function run() {
        if (self::$debug) {
            echo "Starting program...\n";
        }
        $this->reset();

        while ($this->_exitCode == self::NOT_FINISHED) {
            if (!isset($this->_instructions[$this->_pointer])) {
                if (self::$debug) {
                    echo "Program pointer (line {$this->_pointer}) not found!\n";
                }
                $this->_exitCode = self::CODE_NOT_FOUND;
                break;
            }
            if (isset($this->_visited[$this->_pointer])) {
                if (self::$debug) {
                    echo "Already visited line {$this->_pointer} ({$this->_instructions[$this->_pointer]['fulltext']})\n";
                }
                $this->_exitCode = self::CODE_LOOP;
                break;
            }

            $this->_visited[$this->_pointer] = true;
            $this->step();
        }

        return $this->_accumulator;
    }

    private function step() {
        if (self::$debug) {
            #echo "DEBUG: At {$this->_instructions[$this->_pointer]['fulltext']}\n";
        }
        if (method_exists($this, $this->getCurrentCommand())) {
            call_user_func_array(
                [$this, $this->getCurrentCommand()],
                $this->getCurrentArguments()
            );
        } else {
            echo "UNKNOWN COMMAND: {$this->_instructions[$this->_pointer]['fulltext']} (at line {$this->_pointer})\n";
            $this->_exitCode = self::UNKNOWN_COMMAND;
        }
        $this->increase();
    }

    public function increase() {
        $this->_pointer++;
    }

    public function accumulator() {
        return $this->_accumulator;
    }

    public function getCurrentCommand() {
        return $this->_instructions[$this->_pointer]["cmd"] ?? false;
    }

    public function getCurrentArguments() {
        return $this->_instructions[$this->_pointer]["arguments"] ?? [];
    }

    public function replaceCurrentCommand($newCommand) {
        $this->_instructions[$this->_pointer]["cmd"] = $newCommand;
    }

    public function exitCode() {
        return $this->_exitCode;
    }
}
