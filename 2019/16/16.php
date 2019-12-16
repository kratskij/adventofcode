<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file);
$input = $ir->trim()->raw();
$basePattern = [0, 1, 0, -1];

echo "Part 1: " . convert2($input, $basePattern, 100, 0) . "\n";

$newInput = "";
#$inputAsStr = implode("", $input);
for ($i = 0; $i < 10000; $i++) {
    $newInput .= $input;
}
echo "Part 2: " . convert2($newInput, $basePattern, 100, substr($newInput, 0, 7)) . "\n";


function convert2($input, $basePattern, $phases, $offset) {
    echo "Offset: " . $offset . "\n";
    echo "Input length: " . strlen($input)."\n";
    $adds = $subs = [];
    $inputCount = strlen($input);
    for ($c = 0; $c < $inputCount; $c++) {
        $tmp = 0;
        for ($k = 0; $k < $inputCount; $k++) {
            $bPos = (floor((($k+1) % (4 * ($c+1))) / ($c + 1))) % 4;
            if ($bPos == 1) {
                $adds[$c][] = $k;
            } else if ($bPos == 2) {
                $subs[$c][] = $k;
            }

        }
    }
    echo "DONE with prejob\n";
    for ($p = 1; $p <= $phases; $p++) {
        $sum = 0;
        foreach ($adds as $a) {
            $sum += substr($a, -1);
        }
        foreach ($subs as $s) {
            $sum -= substr($s, -1);
        }
        $newInput = array_sum($adds) - array_sum($subs);
        for ($c = 0; $c < $inputCount; $c++) {
            $input[$c] = $newInput
        }
        $newInput = "";
        /*$inputCount = strlen($input);
        for ($c = 0; $c < $inputCount; $c++) {
            $tmp = 0;
            for ($k = 0; $k < $inputCount; $k++) {
                $bPos = (floor((($k+1) % (4 * ($c+1))) / ($c + 1))) % 4;
                $tmp += (int)$input[$k] * $basePattern[$bPos];
                if (($k % 1000) == 0) {
                    echo $k."\n";
                }
            }

            $newInput .= substr($tmp, -1);

        }*/
        $input = $newInput;
        echo "$p: " . substr($input, 0, 8) . "\n";
    }

    return substr($input, $offset, 8);
}



function convert($input, $basePattern, $phases) {
    for ($p = 1; $p <= $phases; $p++) {
        $newInput = [];

        $inputCount = count($input);
        foreach ($input as $c => $value) {
            $tmp = [];
            $newPattern = [];
            while (count($newPattern) < $inputCount + 1) {
                for ($j = 0; $j <= $c; $j++) {
                    $newPattern[] = $basePattern[0];
                }
                for ($j = 0; $j <= $c; $j++) {
                    $newPattern[] = $basePattern[1];
                }
                for ($j = 0; $j <= $c; $j++) {
                    $newPattern[] = $basePattern[2];
                }
                for ($j = 0; $j <= $c; $j++) {
                    $newPattern[] = $basePattern[3];
                }
            }
            array_shift($newPattern);
            #echo "pat:" . implode(",", $newPattern)."\n";
            foreach ($input as $k => $val) {
                $tmp[] = $val * $newPattern[$k];
            }

            $newInput[] = substr(array_sum($tmp), -1);
    #        echo implode(",", $newInput)."\n";
        }
        $input = $newInput;
    }
    return substr(implode("", $input), 0, 8);
}
