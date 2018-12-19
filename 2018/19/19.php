<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->lines();

$instructions = [
    "addr" => function($registers, $a, $b) {
        return $registers[$a]+$registers[$b];
    },
    "addi" => function($registers, $a, $b) {
        return $registers[$a] + $b;
    },
    "mulr" => function($registers, $a, $b) {
        return $registers[$a] * $registers[$b];
    },
    "muli" => function($registers, $a, $b) {
        return $registers[$a] * $b;
    },
    "banr" => function($registers, $a, $b) {
        return ($registers[$a] & $registers[$b]);
    },
    "bani" => function($registers, $a, $b) {
        return ($registers[$a] & $b);
    },
    "borr" => function($registers, $a, $b) {
        return ($registers[$a] | $registers[$b]);
    },
    "bori" => function($registers, $a, $b) {
        return ($registers[$a] | $b);
    },
    "setr" => function($registers, $a, $b) {
        return $registers[$a];
    },
    "seti" => function($registers, $a, $b) {
        return $a;
    },
    "gtir" => function($registers, $a, $b) {
        return ($a > $registers[$b]) ? 1 : 0;
    },
    "gtri" => function($registers, $a, $b) {
        return ($registers[$a] > $b) ? 1 : 0;
    },
    "gtrr" => function($registers, $a, $b) {
        return ($registers[$a] > $registers[$b]) ? 1 : 0;
    },
    "eqir" => function($registers, $a, $b) {
        return ($a == $registers[$b]) ? 1 : 0;
    },
    "eqri" => function($registers, $a, $b) {
        return ($registers[$a] == $b) ? 1 : 0;
    },
    "eqrr" => function($registers, $a, $b) {
        return ($registers[$a] == $registers[$b]) ? 1 : 0;
    }
];

$commands = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$registers = [0,0,0,0,0,0];

$lines = $commands->lines();
$ipReg = (int)explode(" ", array_shift($lines))[1];
$ip = 0;
while (true) {
    if (!isset($lines[$ip])) {
        break;
    }
    $line = $lines[$ip];
    $parts = explode(" ", $line);
    list($opcode, $a, $b, $c) = $parts;
    $registers[$ipReg] = $ip;
    #echo "ip=$ip [" . implode(",", $registers) . "]    $line";
    #$registers[0] = $ip;
    $registers[(int)$c] = $instructions[$opcode]($registers, (int)$a, (int)$b);
    $ip = $registers[$ipReg] + 1;
    #echo "   [" . implode(",", $registers) . "]\n";
    #echo $registers[$ipReg] . " ";
}
echo "   [" . implode(",", $registers) . "]\n";

echo "Part 1: " . $registers[0]."\n";

$registers[0] = 1;
$ip = 0;
while (true) {
    if (!isset($lines[$ip])) {
        break;
    }
    $line = $lines[$ip];
    $parts = explode(" ", $line);
    list($opcode, $a, $b, $c) = $parts;
    $registers[$ipReg] = $ip;
    #echo "ip=$ip [" . implode(",", $registers) . "]    $line";
    #$registers[0] = $ip;
    $registers[(int)$c] = $instructions[$opcode]($registers, (int)$a, (int)$b);
    $ip = $registers[$ipReg] + 1;
    #echo "   [" . implode(",", $registers) . "]\n";
    #echo $registers[$ipReg] . " ";
}
echo "   [" . implode(",", $registers) . "]\n";

echo "Part 1: " . $registers[0]."\n";
