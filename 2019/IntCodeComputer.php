<?php

class End extends Exception {}
class Stalled extends Exception {}

class IntCodeComputer {
    private $_origCode;
    private $_code;
    protected $_lastOutput;
    private $_mode;
    private $_pointer;
    private $_relative;

    public function __construct($code) {
        $this->_origCode = $code;
        $this->_code = $code;
        $this->reset();
    }

    public function reset() {
        $this->_code = $this->_origCode;
        $this->_lastOutout = false;
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

    public function in($input, $loop = true, $mightStall = false) {
        $c = 0;
        while (true) {
            $c++;
            if ($mightStall && $c == 10000) {
                throw new Stalled('Computer is stalled');
            }
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
                    if (!$loop) {
                        return $this->_lastOutput;
                    }
                    break;
                case 4:
                    $this->_lastOutput = $this->indirect($modes);
                    return $this->_lastOutput; // halt
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
                    throw new End("END");
                    return $this->_lastOutput;
                default:
                    throw new Exception("Unknown opcode ($opcode)!");
            }
        }
        throw new Exception('End of program (not opcode 99)');
    }
}
