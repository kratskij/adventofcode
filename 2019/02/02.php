<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file);
$ir->trim(true);

for ($noun = 0; $noun <= 99; $noun++) {
    for ($verb = 0; $verb <= 99; $verb++) {
        $input = $ir->explode(",");
        $input = array_map("intval", $input);
        $input[1] = $noun;
        $input[2] = $verb;

        for ($i = 0; $i < count($input); $i+=4) {
            switch ($input[$i]) {
                case 1:
                    $input[$input[$i+3]] = $input[$input[$i+1]] + $input[$input[$i+2]];
                    break;
                case 2:
                    $input[$input[$i+3]] = $input[$input[$i+1]] * $input[$input[$i+2]];
                    break;
                case 99:
                    break 2;
            }
        }
        if ($input[0] == 19690720) {
            $inputCode = (100 * $noun + $verb);
            die("Part 2: $inputCode\n");

        }
        if ($noun == 12 && $verb == 2) {
            echo "Part 1: " . $input[0]."\n";
        }
    }
}
