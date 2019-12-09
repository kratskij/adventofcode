<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file);
$ir->trim(true);

$code = $ir->explode(",");
$code = $code;

echo "Part 1: " . run($code, ($test ? 0 : 1)) . "\n";
echo "Part 2: " . run($code, ($test ? 0 : 2)) . "\n";

function run($code, $input) {
    $amp = new IntCodeComputer($code, $input);
    $amp->in($input);
    return $amp->out();
}


class IntCodeComputer {
    private $_code;
    private $_lastOutput;
    private $_mode;
    private $_pointer;
    private $_relative;

    public function __construct($code, $input) {
        $this->_code = $code;
        $this->_input = $input;
        $this->_lastOutput = false;
        $this->_pointer = 0;
        $this->_relative = 0;
    }

    public function out() {
        return $this->_lastOutput;
    }

    private function direct(array &$modes) {
        $code = $this->_code[$this->_pointer++] ?? 0;
        if (array_shift($modes) == 2) {
            $code += $this->_relative;
        }
        return $code;
    }

    private function indirect(array &$modes) {
        $mode = array_shift($modes);
        array_unshift($modes, $mode);
        if ($mode == 1) {
            return $this->direct($modes);
        }

        return $this->_code[$this->direct($modes)] ?? 0;
    }

    public function in($input) {
        while (true) {
            $this->_internalPointer = 0;
            $fakeModes = [1];
            $code = $this->direct($fakeModes);
            $opcode = $code % 100;
            $modes = array_reverse(str_split(floor($code / 100)));
            switch ($opcode) {
                case 1:
                    $one = $this->indirect($modes);
                    $two = $this->indirect($modes);
                    $three = $this->direct($modes);
                    $this->_code[$three] = $one + $two;
                    break;
                case 2:
                    $one = $this->indirect($modes);
                    $two = $this->indirect($modes);
                    $three = $this->direct($modes);
                    $this->_code[$three] = $one * $two;
                    break;
                case 3:
                    $this->_code[$this->direct($modes)] = $input;
                    break;
                case 4:
                    $this->_lastOutput = $this->indirect($modes);
                    break;
                case 5:
                    if ($this->indirect($modes) != 0) {
                        $this->_pointer = $this->indirect($modes);
                    } else {
                        $this->direct($modes);
                    }
                    break;
                case 6:
                    if ($this->indirect($modes) == 0) {
                        $this->_pointer = $this->indirect($modes);
                    } else {
                        $this->direct($modes);
                    }
                    break;
                case 7:
                    if ($this->indirect($modes) < $this->indirect($modes)) {
                        $this->_code[$this->direct($modes)] = 1;
                    } else {
                        $this->_code[$this->direct($modes)] = 0;
                    }
                    break;
                case 8:
                    if ($this->indirect($modes) == $this->indirect($modes)) {
                        $this->_code[$this->direct($modes)] = 1;
                    } else {
                        $this->_code[$this->direct($modes)] = 0;
                    }
                    break;
                case 9:
                    $this->_relative += $this->indirect($modes);
                    break;
                case 99:
                    return $this->_lastOutput;
                default:
                    throw new Exception("Unknown opcode ($opcode)!");
            }
        }
        throw new Exception('End of program (not opcode 99)');
    }
}
