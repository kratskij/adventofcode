<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file);
$ir->trim(true);

$code = $ir->explode(",");
$code = array_map("intval", $code);

echo "Part 1: " . getDiagnosticsCode($code, 1) . "\n";
echo "Part 2: " . getDiagnosticsCode($code, 5) . "\n";

function getDiagnosticsCode($code, $input) {
    $lastOutput = "";

    for ($i = 0; $i < count($code); $i++) {
        $opcode = (int)substr($code[$i], -2);
        $modes = str_split(substr(str_pad($code[$i], 5, "0", STR_PAD_LEFT), 0, 3));
        $values = [];
        foreach (array_reverse($modes) as $k => $m) {
            $values[$k] = ($m == 0) ? @$code[$code[$i+$k+1]] : $code[$i+$k+1];
        }

        switch ($opcode) {
            case 1:
                $code[$code[$i+3]] = $values[0] + $values[1];
                $i += 3;
                break;
            case 2:
                $code[$code[$i+3]] = $values[0] * $values[1];
                $i += 3;
                break;
            case 3:
                $code[$code[$i+1]] = $input;
                $i += 1;
                break;
            case 4:
                $lastOutput = $values[0];
                $i += 1;
                break;
            case 5:
                if ($values[0] != 0) {
                    $i = $values[1]-1;
                } else {
                    $i += 2;
                }
                break;
            case 6:
                if ($values[0] == 0) {
                    $i = $values[1]-1;
                } else {
                    $i += 2;
                }
                break;
            case 7:
                if ($values[0] < $values[1]) {
                    $code[$code[$i+3]] = 1;
                } else {
                    $code[$code[$i+3]] = 0;
                }
                $i += 3;
                break;
            case 8:
                if ($values[0] == $values[1]) {
                    $code[$code[$i+3]] = 1;
                } else {
                    $code[$code[$i+3]] = 0;
                }
                $i += 3;
                break;
            case 99:
                break 2;
            default:
                die("DIEEEEE $opcode");
        }
    }

    return $lastOutput;
}
