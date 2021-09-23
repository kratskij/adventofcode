<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);

$input = $ir->regex("(.*)\s\(contains\s(.*)\)");

$p1 = $p2 = false;

$ingrList = [];
$allergenList = [];
$foodList = [];

foreach ($input as $k => $line) {
    list($one, $two) = $line;
    $ingrs = explode(" ", $one);
    $allergens = explode(", ", $two);

    foreach ($ingrs as $ingr) {
        foreach ($allergens as $allergen) {
            $ingrList[$ingr][$allergen] = $allergen;
            $allergenList[$allergen][$ingr] = $ingr;
        }
    }

    $foodList[$k] = [
        "ingrs" => $ingrs,
        "allergens" => $allergens,
    ];
}

$change = true;
while ($change) {
    $change = false;
    foreach ($foodList as $i => $food) {
        foreach ($foodList as $j => $food2) {
            $commonIngrs = array_intersect($food["ingrs"], $food2["ingrs"]);
            $commonAllergens = array_intersect($food["allergens"], $food2["allergens"]);

            foreach ($commonAllergens as $theAllergen) {
                foreach ($allergenList[$theAllergen] as $ingr => $none) {
                    if (!in_array($ingr, $commonIngrs)) {
                        unset($allergenList[$theAllergen][$ingr]);
                        unset($ingrList[$ingr][$theAllergen]);
                        $change = true;
                    }
                }
            }
        }
    }
}

$p1 = 0;
foreach ($foodList as $food) {
    foreach ($food["ingrs"] as $ingr) {
        if (empty($ingrList[$ingr])) {
            $p1++;
        }
    }
}

$change = true;
while ($change) {
    $change = false;
    foreach ($ingrList as $ingr => $allergens) {
        if (count($allergens) == 1) {
            foreach ($ingrList as $ingr2 => $allergens2) {
                if ($ingr2 != $ingr) {
                    $pc = count($ingrList[$ingr2]);
                    $ingrList[$ingr2] = array_diff($ingrList[$ingr2], $allergens);
                    if ($pc != count($ingrList[$ingr2])) {
                        $change = true;
                    }
                }
            }
        }
    }
}
$p2 = [];
foreach ($ingrList as $ingr => $allergens) {
    if (count($allergens) == 1) {
        $p2[$ingr] = reset($allergens);
    }
}
asort($p2);
$p2 = implode(",", array_keys($p2));

echo "P1: $p1\nP2: $p2\n";
