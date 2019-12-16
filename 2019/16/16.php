<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file);
$input = $ir->trim()->raw();

echo "Part 1: " . convert($input, 100) . "\n";

$newInput = "";
#$inputAsStr = implode("", $input);

for ($i = 0; $i < 10000; $i++) {
    $newInput .= $input;
}
echo "Part 2: " . convert($newInput, 100, substr($newInput, 0, 7)) . "\n";


/*for ($i = 0; $i < 4; $i++) {
    $newInput .= $input;
}
echo "Part 2: " . convert2($newInput, $basePattern, 100, 0/* substr($newInput, 0, 2)*/
#) . "\n";


function convert($originalInput, $phases, $offset = 0) {
    $input = substr($originalInput, $offset);
    $inputCount = strlen($input);

    if ($inputCount <= strlen($originalInput) / 2) {
        // Reversed, we can safely accumulate the last half; they will all be ones.
        for ($p = 1; $p <= $phases; $p++) {
            $tmp = 0;
            for ($c = $inputCount - 1; $c >= 0; $c--) {
                $tmp += $input[$c];
                $input[$c] = abs($tmp % 10);
            }
        }

        return substr($input, 0, 8);
    }

    for ($p = 1; $p <= $phases; $p++) {
        $newInput = $input;
        for ($c = 0; $c <= $inputCount; $c++) {
            $tmp = 0;
            $coffsetone = $c + $offset + 1;
            for ($k = $c; $k < $inputCount; $k++) { // no need to calculate the first part; they will all be zeros.
                $bPos = (
                    floor(
                        (
                            ($k + $offset + 1) % (4 * ($coffsetone))
                        ) / $coffsetone
                    )
                ) % 4;

                if ($bPos == 1) {
                    $tmp += (int)$input[$k];
                } else if ($bPos == 3) {
                    $tmp -= (int)$input[$k];
                }
            }
            $newInput[$c] = abs($tmp % 10);
        }
        $input = $newInput;
    }

    return substr($input, 0, 8);
}
