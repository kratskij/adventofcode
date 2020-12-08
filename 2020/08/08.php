<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");
require_once(__DIR__."/../Program.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);

$input = $ir->lines();
$program = new Program($input);

$programs = [$program];

while ($cmd = $program->getCurrentCommand()) {
    if ($cmd == "jmp") {
        $clone = clone $program;
        $clone->replaceCurrentCommand("nop");
        $programs[] = $clone;
    } else if ($cmd == "nop") {
        $clone = clone $program;
        $clone->replaceCurrentCommand("jmp");
        $programs[] = $clone;
    }
    $program->increase();
}

Program::$debug = false;

$p1 = $p2 = false;
foreach ($programs as $program) {
    $program->run();

    switch ($program->exitCode()) {
        case Program::CODE_LOOP:
            if (!$p1) {
                $p1 = $program->accumulator();
            }
            continue 2;
        case Program::CODE_NOT_FOUND:
            $p2 = $program->accumulator();
            break 2;
        default:
            echo "Unknown exit code: " . $program->exitCode();
            exit;
    }
}

echo "P1: $p1\nP2: $p2\n";
