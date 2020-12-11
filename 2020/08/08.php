<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");
require_once(__DIR__."/../Program.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->lines();

class Accumulator extends Program {
    private $_accumulator = 0;

    public function __construct($input) {
        parent::__construct($input);
    }

    protected function acc($value) {
        $this->_accumulator += $value;
    }

    public function reset() {
        parent::reset();
        $this->_accumulator = 0;
    }

    public function accumulator() {
        return $this->_accumulator;
    }

    public function run() {
        parent::run();
        return $this->_accumulator;
    }
}

Program::$debug = false;

$program = new Accumulator($input);
$p1 = $p2 = false;

$program->run();
if ($program->exitCode() == Program::CODE_LOOP) {
    $p1 = $program->accumulator();
} else {
    echo "Exit code " . $program->exitCode() . "\n";
}
$program->reset();


foreach ($input as $lineNumber => $instruction) {
    $cmd = explode(" ", $instruction)[0];
    if ($cmd == "jmp" || $cmd == "nop") {
        $convertTo = ($cmd == "jmp") ? "nop" : "jmp";
        $instructions = $input;
        $instructions[$lineNumber] = str_replace($cmd, $convertTo, $instructions[$lineNumber]);
        $clone = new Accumulator($instructions);
        $accumulator = $clone->run();
        if ($clone->exitCode() == Program::CODE_NOT_FOUND) {
            $p2 = $accumulator;
            break;
        }
    }
}


echo "P1: $p1\nP2: $p2\n";
