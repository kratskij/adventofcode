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
$lines = $commands->lines();

$ipReg = (int)explode(" ", array_shift($lines))[1];

foreach ($lines as $k => $line) {
    $parts = explode(" ", $line);
    list($opcode, $a, $b, $c) = $parts;
    $lines[$k] = ["opcode" => $opcode, "a" => (int)$a, "b" => (int)$b, "c" => (int)$c];
}

$registers = [0,0,0,0,0,0];

$ip = 0;
while (isset($lines[$ip])) {
    $registers[$ipReg] = $ip;
    $registers[$lines[$ip]["c"]] = $instructions[$lines[$ip]["opcode"]]($registers, $lines[$ip]["a"], $lines[$ip]["b"]);
    $ip = $registers[$ipReg] + 1;
}
echo "   [" . implode(",", $registers) . "]\n";

echo "Part 1: " . $registers[0]."\n";

$registers = [1,0,0,0,0,0];
$ip = 0;
$hei = 0;
while (isset($lines[$ip])) {
    echo "$ip ... [" . implode(",", $registers) . "]\n";
    if ($ip == 11) {
        $registers[5] = $registers[2];
        $ip = 3;
        $hei++;
    } else if ($ip == 15) {
        $registers[4] = $registers[2];
        if ($hei === 2) {
            die();
        }
        $ip = 3;
        $hei++;
    } else {
        $registers[$ipReg] = $ip;
        $registers[$lines[$ip]["c"]] = $instructions[$lines[$ip]["opcode"]]($registers, $lines[$ip]["a"], $lines[$ip]["b"]);
        $ip = $registers[$ipReg] + 1;
    }

/*
    if (count($lastInstructions) == count($convertion["instructions"])) {
        array_shift($lastInstructions);
    }

    $lastInstructions[] = $ip;

    echo "just ran $ip:   (" . $lines[$ip]["opcode"] . " " . $lines[$ip]["a"] . ", " . $lines[$ip]["b"]. ", " . $lines[$ip]["c"] . ")    [" . implode(",", $registers) . "]\n";
    if ($ip == 9 && implode(",", $convertion["instructions"]) == implode(",", $lastInstructions)) {
        $lastInstructions = [];
        $regCopy = $registers;
        while (true) {
            echo "converting! [" . implode(",", $regCopy) . "] to ...";
            $regCopy[$convertion["convertTo"]["c"]] = $instructions[$convertion["convertTo"]["opcode"]]($regCopy, $convertion["convertTo"]["a"], $convertion["convertTo"]["b"]);
            echo "... [" . implode(",", $regCopy) . "]\n";

            $regCopy2 = $regCopy;
            foreach ($convertion["instructions"] as $instr) {
                #echo "     ... [" . implode(",", $regCopy2) . "] ...\n";
                $regCopy2[$lines[$instr]["c"]] = $instructions[$lines[$instr]["opcode"]]($regCopy2, $lines[$instr]["a"], $lines[$instr]["b"]);
            }
            #echo "... [" . implode(",", $regCopy2) . "]\n";
            foreach ($convertion["compareRegisters"] as $r) {
                if ($regCopy2[$r] != $regCopy[$r]) {
                    echo "breaking! [" . implode(",", $regCopy) . "] is not equal to [" . implode(",", $regCopy2) . "] (at $r)\n";
                    $regCopy[$convertion["revert"]["c"]] = $instructions[$convertion["revert"]["opcode"]]($regCopy, $convertion["revert"]["a"], $convertion["revert"]["b"]);
                    $registers = $regCopy;
                    #echo "breaking! [" . implode(",", $registers) . "]\n";
                    break 2;
                }
            }

            //we passed one skip
            $registers = $regCopy;
        }
    }*/
}
echo "   [" . implode(",", $registers) . "]\n";

echo "Part 1: " . $registers[0]."\n";


/*
$registers = [1,0,0,0,0,0];
$ip = 0;
while (true) {
    if (!isset($lines[$ip])) {
        break;
    }
    $line = $lines[$ip];
    $parts = explode(" ", $line);
    list($opcode, $a, $b, $c) = $parts;
    $registers[$ipReg] = $ip;
    #echo "\nip=$ip [" . implode(",", $registers) . "]    $line";
    #$registers[0] = $ip;
    $registers[(int)$c] = $instructions[$opcode]($registers, (int)$a, (int)$b);
    $ip = $registers[$ipReg] + 1;
    #echo "   [" . implode(",", $registers) . "]\n";
    #echo $registers[$ipReg] . " ";
}
echo "   [" . implode(",", $registers) . "]\n";

echo "Part 1: " . $registers[0]."\n";
*/
