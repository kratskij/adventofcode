<?php

require_once(__DIR__."/../inputReader.php");

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

$alphabets = [
    "ΑΒΓΔΕΖΗΘΙΚΛΜΝΞΟΠΡΣΤΥΦΧΨΩ",
    "αβγδεζηθικλμνξοπρστυφχψω",
];

$spellings = [ "Οδυσσευσ", "Οδυσσεωσ", "Οδυσσει", "Οδυσσεα", "Οδυσσευ" ];

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->lines();

$ans = 0;
foreach ($input as $k => $line) {
    $line = mb_ereg_replace("ς", "σ", $line);
    for ($i = 0; $i < 24; $i++) {
        $new = "";
        for ($j = 0; $j < mb_strlen($line); $j++) {
            $char = mb_substr($line, $j, 1);
            $found = false;
            foreach ($alphabets as $alphabet) {
                $pos = mb_strpos($alphabet, $char);
                if ($pos !== false) {
                    $new = $new . mb_substr($alphabet, ($pos + $i) % 24, 1);
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $new = $new . $char;
            }
        }

        foreach ($spellings as $spelling) {
            if (mb_stripos($new, $spelling) !== false) {
                $ans += $i;
                continue 3;
            }
        }
    }
}

echo "Answer: $ans\n";
