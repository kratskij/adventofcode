<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file);
$ir->trim(true);

$code = $ir->explode(",");
$code = array_map("intval", $code);

echo "Part 1: " . run($code, [0,1,2,3,4], Amp::MODE_COMPLETE) . "\n";
echo "Part 2: " . run($code, [5,6,7,8,9], Amp::MODE_PAUSE) . "\n";


function run($code, $validValues, $mode) {
    $possiblePhases = array_map(
        function($phase) use ($validValues) {
            return str_split(str_pad($phase, count($validValues), "0", STR_PAD_LEFT));
        },
        array_filter(
            range(0, (int)str_repeat("9", count($validValues))),
            function($i) use ($validValues) {
                $valid = true;
                foreach ($validValues as $v) {
                    $valid = $valid && substr_count($i, $v) == 1;
                }
                return $valid;
            }
        )
    );

    $max = -PHP_INT_MAX;

    foreach ($possiblePhases as $phases) {
        $amps = [];
        foreach ($phases as $key => $phase) {
            $amps[$key] = new Amp($code, $phase, $mode);
            $amps[$key]->in(($key == 0) ? 0 : $amps[$key-1]->out());
        }

        if ($mode == Amp::MODE_PAUSE) { // we need to repeat until the last amp returns something
            $runAll = function (&$amps, $in = 0) {
                $last = $amps[max(array_keys($amps))];
                foreach ($amps as $key => $amp) {
                    $in = isset($amps[$key-1]) ? $amps[$key-1]->out() : $amps[max(array_keys($amps))]->out();
                    $lastValue = $amp->in($in);
                }
                return $lastValue;
            };

            $lastOut = $runAll($amps, 0);
            while ($lastOut === false) {
                $lastOut = $runAll($amps, $amps[4]->out());
            }
        }
        $max = max($max, $amps[4]->out());
    }

    return $max;
}


class Amp {
    private $_code;
    private $_phase;
    private $_lastOutput;
    private $_mode;

    const MODE_COMPLETE = 1;
    const MODE_PAUSE = 2;

    public function __construct($code, $phase, $mode) {
        $this->_code = $code;
        $this->_phase = $phase;
        $this->_origPhase = $phase;
        $this->_mode = $mode;
        $this->_lastOutput = false;
        $this->_pointer = 0;

    }

    public function __toString() {
        return "Phase: " . $this->_origPhase;
    }
    public function out() {
        return $this->_lastOutput;
    }

    private function val($add = 0) {
        return $this->_code[$this->_pointer + $add];
    }
    public function in($input) {
        for (; $this->_pointer < count($this->_code); $this->_pointer++) {
            $opcode = (int)substr($this->val(), -2);
            #echo $opcode."\n";
            $modes = str_split(substr(str_pad($this->val(), 5, "0", STR_PAD_LEFT), 0, 3));
            $values = [];
            foreach (array_reverse($modes) as $k => $m) {
                $values[$k] = ($m == 0) ? @$this->_code[$this->val($k+1)] : $this->val($k+1);
            }
            #echo implode(",", $this->_code)."\n";//$opcode."\n";
            switch ($opcode) {
                case 1:
                    $this->_code[$this->val(3)] = $values[0] + $values[1];
                    $this->_pointer += 3;
                    break;
                case 2:
                    $this->_code[$this->val(3)] = $values[0] * $values[1];
                    $this->_pointer += 3;
                    break;
                case 3:
                    if ($this->_phase !== null) {
                        #echo "PHASE!! $phase\n";
                        $this->_code[$this->val(1)] = $this->_phase;
                        $this->_phase = null;
                    } else {
                        $this->_code[$this->val(1)] = $input;
                    }
                    $this->_pointer += 1;
                    break;
                case 4:
                    $this->_lastOutput = $values[0];
                    switch ($this->_mode) {
                        case self::MODE_COMPLETE:
                            $this->_pointer++;
                            break 2;
                        case self::MODE_PAUSE:
                            $this->_pointer += 2;
                            return false;
                    }
                case 5:
                    if ($values[0] != 0) {
                        $this->_pointer = $values[1]-1;
                    } else {
                        $this->_pointer += 2;
                    }
                    break;
                case 6:
                    if ($values[0] == 0) {
                        $this->_pointer = $values[1]-1;
                    } else {
                        $this->_pointer += 2;
                    }
                    break;
                case 7:
                    if ($values[0] < $values[1]) {
                        $this->_code[$this->val(3)] = 1;
                    } else {
                        $this->_code[$this->val(3)] = 0;
                    }
                    $this->_pointer += 3;
                    break;
                case 8:
                    if ($values[0] == $values[1]) {
                        $this->_code[$this->val(3)] = 1;
                    } else {
                        $this->_code[$this->val(3)] = 0;
                    }
                    $this->_i += 3;
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
