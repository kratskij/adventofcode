<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->lines();

$opcodes = [];

$part1 = 0;

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

while (count($input)) {
    $line = array_shift($input);
    $parts = explode(": ", $line);

    if ($parts[0] == "Before") {
        $registers = array_map("intval", explode(" ", trim($parts[1], "[]")));

        $code = explode(" ", array_shift($input));
        $opcode = (int)array_shift($code);
        $a = (int)array_shift($code);
        $b = (int)array_shift($code);
        $c = (int)array_shift($code);

        $after = array_map("intval", explode(" ", trim(explode(": ", array_shift($input))[1], " []")));

        $possibleCodes = [];
        foreach ($instructions as $alias => $func) {
            if ($after[$c] == $func($registers, $a, $b)) {
                $possibleCodes[$alias] = $alias;
            }
        }
        if (count($possibleCodes) >= 3) {
            $part1++;
        }
        $opcodes[$opcode] = (isset($opcodes[$opcode])) ? array_intersect_key($possibleCodes, $opcodes[$opcode]) : $possibleCodes;
    }
}

// If our set is a full sub-set of another set, let's remove it from the other set
while (array_sum(array_map("count", $opcodes)) > count($opcodes)) {
    foreach ($opcodes as $oneKey => $one) {
        foreach ($opcodes as $twoKey => &$two) {
            if (
                $oneKey != $twoKey &&
                count(array_intersect($one, $two)) == count($one) &&
                count(array_intersect($two, $one)) != count($two)
            ) {
                $two = array_diff_key($two, $one);
            }
        }
    }
}
$opcodes = array_map("reset", $opcodes);

$commands = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . "instructions"))->trim(true);
$registers = [0,0,0,0];

foreach ($commands->lines() as $line) {
    list($opcode, $a, $b, $c) = array_map("intval", explode(" ", $line));
    $registers[$c] = $instructions[$opcodes[$opcode]]($registers, $a, $b);
}

echo "Part 1: $part1\n";
echo "Part 2: " . $registers[0]."\n";
