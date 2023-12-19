<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");
#require_once __DIR__."/../Util.php";

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim();

$input = $ir->explode("\n\n");

$rules = [];

const LT = "<";
const GT = ">";
const EQ = "=";

$startRanges = [
    "x" => [1,4000],
    "m" => [1,4000],
    "a" => [1,4000],
    "s" => [1,4000],
];

foreach (explode("\n", $input[0]) as $line) {
    $parts = array_filter(preg_split("/[\{\,\}]/", $line));
    $name = array_shift($parts);
    foreach ($parts as $part) {
        $p = explode(":", $part);
        if (count($p) == 2) {
            foreach ([LT, GT] as $cmp) {
                $px = explode($cmp, $p[0]);
                $char = LT;
                if (count($px) == 2) {
                    break;
                }
            }
            $rules[$name][] = [$px[0], $cmp, (int)$px[1], $p[1]];
        } else {
            $rules[$name][] = ["1", EQ, "1", $p[0]];
        }
    }
}
$acceptedRanges = [];
$p2 = countAccepted($rules, $startRanges, $acceptedRanges);

$p2 = 0;
$known = [];
foreach ($acceptedRanges as $r) {
    foreach ($r as $r2) {
        echo ($r2[0] - $r2[1] + 1) . ": " . ($r2[1] - $r2[0] + 1) . ", ";
    }
    echo "\n";

    $valid = true;
    $idx = md5(json_encode($r));
    if (isset($known[$idx])) {
        echo "known\n";
        continue;
    }
    $known[$idx] = true;
    $req = 0;
    foreach ($acceptedRanges as $idx => $r2) {
        $eq = 0;
        foreach (["x", "m", "a", "s"] as $idx2 => $type) {
            if ($idx2 == $idx) { continue; }
            if ($r[$type][0] == $r2[$type][0] && $r[$type][1] == $r2[$type][1]) {
                $eq++;
            }
            if ($r[$type][0] >= $r2[$type][0] && $r[$type][1] <= $r2[$type][1]) {
                $valid = false;
            }
        }
        $req = max($req, $eq);
    }
    
    echo $req . " " . $valid . "\n";
    #if ($req <= 3 && $valid) {
        $a = [];
        foreach ($r as $v) {
            $a[] = $v[1] - $v[0] - 1;
        }
        $p2 += array_product($a);
    #}
}
#var_dump($acceptedRanges);die();


$parts = [];
$p1 = 0;
foreach (explode("\n", $input[1]) as $line) {
    $line = explode(",", str_replace(["{", "}"], "", $line));
    $material = [];
    foreach ($line as $prop) {
        $prop = explode("=", $prop);
        $material[$prop[0]] = $prop[1];
    }

    if (isAccepted($material, $rules)) {
        $p1 += array_sum($material);
    }
    $materials[] = $material;
}

function countAccepted($rules, $materials, &$acceptedRanges, $at = "in") {
    if ($at == "A") {
        $acceptedRanges[] = $materials;
        
    } else if ($at == "R") {
        return;
    }

    foreach ($rules[$at] as $idx => $rule) {
        [$a, $cmp, $b, $dst] = $rule;
        $materialsCp = $materials;
        switch ($cmp) {
            case GT:
                $materialsCp[$a][0] = max($materialsCp[$a][0], $b + 1);
                countAccepted($rules, $materialsCp, $acceptedRanges, $dst);
                break;
            case LT:
                $materialsCp[$a][1] = min($materialsCp[$a][1], $b - 1);
                countAccepted($rules, $materialsCp, $acceptedRanges, $dst);
                break;
            case EQ:
                countAccepted($rules, $materialsCp, $acceptedRanges, $dst);
                break;
        }
    }

    return $acceptedRanges;
}

function isAccepted($part, $rules, $at = "in") {
    if ($at == "A") {
        return true;
    } else if ($at == "R") {
        return false;
    }
    foreach ($rules[$at] as $rule) {
        [$a, $cmp, $b, $dst] = $rule;

        switch ($cmp) {
            case GT:
                if ($part[$a] > $b) {
                    return isAccepted($part, $rules, $dst);
                }
                break;
            case LT:
                if ($part[$a] < $b) {
                    return isAccepted($part, $rules, $dst);
                }
                break;
            case EQ:
                return isAccepted($part, $rules, $dst);
        }
    }

    return false;
}

echo "P1: $p1\nP2: $p2\n";
