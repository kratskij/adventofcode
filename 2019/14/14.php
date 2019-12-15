<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file);
$input = $ir->trim(true)->lines();

$conv = [];

foreach ($input as $k => $i) {
    list($from, $to) = explode(" => ", $i);

    list($toCount, $toChem) = explode(" ", $to);
    $conv[$toChem] = [
        "count" => (int)$toCount,
        "from" => []
    ];
    foreach (explode(", ", $from) as $f) {
        list($fromCount, $fromChem) = explode(" ", $f);
        $conv[$toChem]["from"][$fromChem] = (int)$fromCount;
    };
}

$stock = [];
$oresPerFuel = create($conv, "FUEL", $stock);
echo "Part 1: $oresPerFuel\n";

$ores = $origOres = 1000000000000;
$fuels = $oresNeeded = 0;
$stock = $states = [];


//oh well. see you in about an hour.
while (true) {
    $oresNeeded = create($conv, "FUEL", $stock);
    if ($ores < $oresNeeded) {
        break;
    }

    //this works on some examples, but not the input data.
    if ($states !== false) {
        $hash = md5(implode(",", $stock) . ",$oresNeeded");
        #echo implode(",", $stock) . ",$oresNeeded\n";
        if (isset($states[$hash])) {
            echo "HASH AT $fuels\n";
            $oresNeededPrLoop = $origOres - $ores;
            $fuelsPerLoop = $fuels;
            $loops = floor($ores / $oresNeededPrLoop);
            $ores -= $loops * $oresNeededPrLoop;
            $fuels += $loops * $fuelsPerLoop;

            $states = false;
        }
        $states[$hash] = true;
    }
    $fuels++;
    $ores -= $oresNeeded;
}
echo "Part 2: $fuels\n";
var_Dump($ores, $oresNeeded);

function create($conv, $name, &$stock) {
    $el = $conv[$name];
    $sum = 0;

    foreach ($el["from"] as $sub => $count) {
        if ($sub == "ORE") {
            return $count;
        }
        if (!isset($stock[$sub])) {
            $stock[$sub] = 0;
        }
        while ($stock[$sub] < $count) {
            $sum += create($conv, $sub, $stock);
            $stock[$sub] += $conv[$sub]["count"];
        }
        $stock[$sub] -= $count;
    }

    return $sum;
}
